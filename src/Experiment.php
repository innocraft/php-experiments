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
use InvalidArgumentException;
use InnoCraft\Experiments\Variations\StandardVariation;

class Experiment {

    const ORIGINAL_VARIATION_NAME = 'original';
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
            if (!$this->variations->exists(Experiment::ORIGINAL_VARIATION_NAME)) {
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

    public function getExperimentName()
    {
        return $this->name;
    }

    public function forceVariationName($variationName)
    {
        $this->storage->set('experiment', $this->name, $variationName);
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getVariations()
    {
        return $this->variations;
    }

    public function getStorage()
    {
        return $this->storage;
    }

    public function shouldTrigger()
    {
        return $this->getActivatedVariation() !== self::DO_NOT_TRIGGER;
    }

    public function getActivatedVariation()
    {
        if (!$this->filter->shouldTrigger()) {
            return self::DO_NOT_TRIGGER;
        }

        $variationName = $this->storage->get('experiment', $this->name);

        if ($variationName && $this->variations->exists($variationName)) {
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

    public static function trackVariationActivation($tracker, $experimentName, $variation)
    {
        // we do not use an interface here for simplicity so it is not needed to use an adapter or something
        // for Piwik tracker
        if ($tracker && method_exists($tracker, 'doTrackEvent')) {
            // eg PiwikTracker
            $tracker->doTrackEvent('abtesting', $experimentName, $variation);
        } else {
            throw new InvalidArgumentException('The given tracker does not implement the doTrackEvent method');
        }
    }

    /**
     * Do not pass variables from $_GET or $_POST etc. Make sure to escape the variables before passing them
     * to this method as you would otherwise risk an XSS.
     * @param $experimentName
     * @param $variation
     * @return string
     */
    public static function getTrackingScript($experimentName, $variation)
    {
        return sprintf('<script type="text/javascript">_paq.push(["AbTesting::enter", {experiment: "%s", variation: "%s"}]);</script>', $experimentName, $variation);
    }

}