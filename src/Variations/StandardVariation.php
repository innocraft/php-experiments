<?php
/**
 * InnoCraft Ltd - We are the makers of Piwik Analytics, the leading open source analytics platform.
 *
 * @link https://www.innocraft.com
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html LGPL v3.0
 */

namespace InnoCraft\Experiments\Variations;

use InvalidArgumentException;

class StandardVariation implements VariationInterface {

    /**
     * @var array
     */
    protected $variation;

    /**
     * @param array $variation eg array('name' => 'blueColor', 'percentage' => 50).
     *                            A name has to be given and can be also an ID, eg "4". Percentage is optional.
     *                            If given, it defines how much traffic this variation should get. For example defining
     *                            50 means, this variation will be activated in 50% of overall experiment activations.
     *
     * @throws InvalidArgumentException If no variation is given
     */
    public function __construct($variation)
    {
        if (!isset($variation['name']) || $variation['name'] === false || $variation['name'] === '') {
            throw new InvalidArgumentException('No variation name is given');
        }

        $this->variation = $variation;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->variation['name'];
    }

    /**
     * @inheritdoc
     */
    public function getPercentage()
    {
        if (isset($this->variation['percentage']) && $this->variation['percentage'] !== false) {
            return (int) $this->variation['percentage'];
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        // we do nothing by default. Usually it is used to get the name of the variation and then do some logic based
        // on it. Eg:
        // if ($activatedVariation->getName() == 'blueColor') { echo 'blue'; } else { echo 'green'; }
    }

}