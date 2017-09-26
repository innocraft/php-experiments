<?php
/**
 * InnoCraft Ltd - We are the makers of Piwik Analytics, the leading open source analytics platform.
 *
 * @link https://www.innocraft.com
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html LGPL v3.0
 */

namespace InnoCraft\Experiments;

use InnoCraft\Experiments\Filters\DefaultFilters;
use InnoCraft\Experiments\Filters\FilterInterface;
use InnoCraft\Experiments\Storage\Cookie;
use InnoCraft\Experiments\Storage\StorageInterface;
use InnoCraft\Experiments\Variations\VariationInterface;
use InvalidArgumentException;
use InnoCraft\Experiments\Variations\StandardVariation;
use Exception;

/**
 * Lets you create a new experiment to run an A/B test or a split test.
 */
class Experiment {

    /**
     * Defines the name of the original version.
     */
    const ORIGINAL_VARIATION_NAME = 'original';

    /**
     * Instead of the word 'original', one can also set '0' to mark a variation as the original version.
     */
    const ORIGINAL_VARIATION_ID = '0';

    /**
     * Is returned by {@link getActivatedVariation()} when no variation should be activated.
     */
    const DO_NOT_TRIGGER = null;

    /**
     * @var int|string
     */
    private $name;

    /**
     * @var Variations
     */
    private $variations;

    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * Creates a new experiment
     *
     * @param string $experimentNameOrId Can be any experiment name or an id of the experiment (eg as given by A/B Testing for Piwik)
     * @param array|VariationInterface[] $variations
     * @param array $config
     */
    public function __construct($experimentNameOrId, $variations, $config = [])
    {
        if (!isset($experimentNameOrId) || $experimentNameOrId === false || $experimentNameOrId === '') {
            throw new InvalidArgumentException('no experimentNameOrId given');
        }

        $this->name = $experimentNameOrId;

        if ($variations instanceof Variations) {
            $this->variations = $variations;
        } else {
            $this->variations = new Variations($experimentNameOrId, $variations);

            // in Piwik A/B Testing there is always an original variation, we need to force the existence here.
            // if you do not want to have this behaviour, instead pass an instance of Variations.
            if (!$this->variations->exists(Experiment::ORIGINAL_VARIATION_NAME)
                && !$this->variations->exists(Experiment::ORIGINAL_VARIATION_ID)) {
                $this->variations->addVariation(new StandardVariation(['name' => Experiment::ORIGINAL_VARIATION_NAME]));
            }
        }

        if (isset($config['storage']) && $config['storage'] instanceof StorageInterface) {
            $this->storage = $config['storage'];
        } elseif (isset($config['storage'])) {
            throw new InvalidArgumentException('storage needs to be an instance of StorageInterface');
        } else {
            $this->storage = new Cookie();
        }

        if (isset($config['filter']) && $config['filter'] instanceof FilterInterface) {
            $this->filter = $config['filter'];
        } elseif (isset($config['filter'])) {
            throw new InvalidArgumentException('filter config needs to be an instance of FilterInterface');
        } else {
            $this->filter = new DefaultFilters($this->name, $this->storage, $config);
        }
    }

    /**
     * Get the name of this experiment.
     *
     * @return int|string
     */
    public function getExperimentName()
    {
        return $this->name;
    }

    /**
     * Forces the activation of the given variation name.
     *
     * @param string $variationName
     */
    public function forceVariationName($variationName)
    {
        $this->storage->set('experiment', $this->name, $variationName);
    }

    /**
     * Detect whether any variation, including the original version, should be activated.
     *
     * Returns true if a variation should and will be activated when calling eg {@link getActivatedVariation()}
     * or {@link run()}, false if no variation will be activated.
     *
     * @return bool
     */
    public function shouldTrigger()
    {
        return $this->getActivatedVariation() !== self::DO_NOT_TRIGGER;
    }

    /**
     * Get the activated variation for this experiment, or null if no variation was activated because of a set filter.
     * For example when the user does not take part in the experiment or when a scheduled date prevents the activation
     * of a variation. Returns the activated variation if no filter "blocked" it. On the first request, a variation
     * will be randomly chosen unless it was forced by {@link forceVariationName()}. On all subsequent requests
     * it will reuse the variation that was activated on the first request.
     *
     * @return VariationInterface|null
     */
    public function getActivatedVariation()
    {
        if (!$this->filter->shouldTrigger()) {
            return self::DO_NOT_TRIGGER;
        }

        $variationName = $this->storage->get('experiment', $this->name);

        if (($variationName || $variationName === '0' || $variationName === 0) && $this->variations->exists($variationName)) {
            return $this->variations->get($variationName);
        }

        $variation = $this->variations->selectRandomVariation();

        if ($variation) {
            $this->forceVariationName($variation->getName());

            return $variation;
        }

        // when no variation exists
        return self::DO_NOT_TRIGGER;
    }

    /**
     * Get the set filter.
     *
     * @return FilterInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Get the set variations.
     *
     * @return Variations
     */
    public function getVariations()
    {
        return $this->variations;
    }

    /**
     * Get the set storage.
     * @return Cookie|StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Tracks the activation of a variation using for example the Piwik Tracker. This lets Piwik know which variation
     * was activated and should be used if you track your application using the Piwik Tracker server side. If you are
     * usually tracking using the JavaScript Tracker, have a look at {@link getTrackingScript()}.
     *
     * @param \stdClass|\PiwikTracker $tracker   The passed object needs to implement a `doTrackEvent` method accepting
     *                                           three parameters $category, $action, $name
     */
    public function trackVariationActivation($tracker)
    {
        // we do not use an interface here for simplicity so it is not needed to use an adapter or something
        // for Piwik tracker
        if ($tracker && method_exists($tracker, 'doTrackEvent')) {
            $variation = $this->getActivatedVariation();

            if ($variation === self::DO_NOT_TRIGGER) {
                return;
            }

            // eg PiwikTracker
            $tracker->doTrackEvent('abtesting', $this->getExperimentName(), $variation->getName());
        } else {
            throw new InvalidArgumentException('The given tracker does not implement the doTrackEvent method');
        }
    }

    /**
     * Returns the JavaScript tracking code that you can echo in your website to let Piwik know which variation was
     * activated server side.
     *
     * Do not pass variables from $_GET or $_POST etc. Make sure to escape the variables before passing them
     * to this method as you would otherwise risk an XSS.
     *
     * @param string $experimentName  ExperimentName and VariationName needs to be passed cause we do not yet have a way
     *                                here to properly escape it to prevent XSS.
     * @param string $variationName
     * @return string  The Piwik tracking code including the `<script>` elements and _paq.push().
     */
    public function getTrackingScript($experimentName, $variationName)
    {
        return sprintf('<script type="text/javascript">_paq.push(["AbTesting::enter", {experiment: "%s", variation: "%s"}]);</script>', $experimentName, $variationName);
    }

    /**
     * Generates a random integer by using the best method available.
     *
     * @param int $min Minimum value
     * @param int $max Maximum value
     * @return int|null
     */
    public static function getRandomInt($min = 0, $max = 999999)
    {
        $val = null;

        if (function_exists('random_int')) {
            try {
                if (!isset($max)) {
                    $max = PHP_INT_MAX;
                }
                $val = random_int($min, $max);
            } catch (Exception $e) {
                // eg if no crypto source is available
                $val = null;
            }
        }

        if (!isset($val)) {
            if (function_exists('mt_rand')) {
                $val = mt_rand($min, $max);
            } else {
                $val = rand($min, $max);
            }
        }
        return $val;
    }
}
