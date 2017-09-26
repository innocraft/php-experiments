<?php
/**
 * InnoCraft Ltd - We are the makers of Piwik Analytics, the leading open source analytics platform.
 *
 * @link https://www.innocraft.com
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html LGPL v3.0
 */

namespace InnoCraft\Experiments;

use InvalidArgumentException;
use InnoCraft\Experiments\Variations\CallableVariation;
use InnoCraft\Experiments\Variations\UrlRedirectVariation;
use InnoCraft\Experiments\Variations\StandardVariation;
use InnoCraft\Experiments\Variations\VariationInterface;

class Variations {

    /**
     * @var VariationInterface[]
     */
    private $variations;

    /**
     * @var string
     */
    private $experimentName;

    /**
     * Variations constructor.
     *
     * @param string $experimentName
     * @param array|VariationInterface[] $variations
     */
    public function __construct($experimentName, $variations)
    {
        $this->experimentName = $experimentName;

        $this->setVariations($variations);
    }

    /**
     * Adds a new variation to the set of existing variations.
     *
     * @param array|VariationInterface $variation
     */
    public function addVariation($variation)
    {
        if (is_array($variation)) {
            // could be moved to a factory or so
            if (isset($variation['url'])) {
                $this->variations[] = new UrlRedirectVariation($this->experimentName, $variation);
            } elseif (isset($variation['callable'])) {
                $this->variations[] = new CallableVariation($variation);
            } else {
                $this->variations[] = new StandardVariation($variation);
            }
        } elseif ($variation instanceof VariationInterface) {
            $this->variations[] = $variation;
        } else {
            throw new InvalidArgumentException('A variation needs to be either an array of an instance of VariationInterface');
        }
    }

    /**
     * Set (overwrite) all existing variations by the given variations.
     *
     * @param array|VariationInterface[] $variations
     */
    public function setVariations($variations)
    {
        $this->variations = [];

        $variations = (array) $variations;

        foreach ($variations as $variation) {
            $this->addVariation($variation);
        }
    }

    /**
     * Get all set variations.
     *
     * @return VariationInterface[]
     */
    public function getVariations()
    {
        return $this->variations;
    }

    protected function getNumVariations()
    {
        return count($this->variations);
    }

    protected function getVariationDefaultPercentage()
    {
        $percentageUsed = 100;

        $numVariations = $this->getNumVariations();

        foreach ($this->variations as $variation) {
            $percentage = $variation->getPercentage();
            if (isset($percentage)) {
                // a fixed percentage was specified, we respect this percentage
                $numVariations--;
                $percentageUsed = $percentageUsed - $percentage;
            }
        }

        if ($percentageUsed < 0 || !$numVariations) {
            $result = 0;
        } else {
            // and then we share the remaining percentage equally across other remaining variations
            // where no percentage was specified
            $result = (int) ($percentageUsed / $numVariations);
        }

        return $result;
    }

    /**
     * Chooses randomly a variation from the set of existing variations. Each variation may set a percentage to
     * allocate more or less traffic to each variation. By default all variation share the traffic equally.
     *
     * @return VariationInterface|null   null if no variations are set
     */
    public function selectRandomVariation()
    {
        if (!empty($this->variations)) {
            $defaultPercentage = $this->getVariationDefaultPercentage();
            $indexes = [];

            foreach ($this->variations as $index => $variation) {
                $percentage = $variation->getPercentage();
                if (!isset($percentage)) {
                    $percentage = $defaultPercentage;
                }

                for ($j = 0; $j < $percentage; $j++) {
                    $indexes[] = $index;
                }
            }

            $index = Experiment::getRandomInt(0, count($indexes) - 1);
            $variationIndex = $indexes[$index];

            return $this->variations[$variationIndex];
        }
    }

    /**
     * Detects whether a variation with the given name exists in the pool of set variations.
     *
     * @param string $variationName
     * @return bool
     */
    public function exists($variationName)
    {
        $variation = $this->get($variationName);

        return !empty($variation);
    }

    /**
     * Get the instance of a set variation by its variation name. If no variation matches the given name, null will be
     * returned.
     *
     * @param string $variationName
     * @return VariationInterface|null
     */
    public function get($variationName)
    {
        foreach ($this->variations as $variation) {
            // we do not use == as we might deal with integers vs string and still want to match eg
            // variationId 2 with '2'
            if ($variation->getName() == $variationName) {
                return $variation;
            }
        }
    }

}