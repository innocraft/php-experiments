<?php
/**
 * InnoCraft Ltd - We are the makers of Piwik Analytics, the leading open source analytics platform.
 *
 * @link https://www.innocraft.com
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html LGPL v3.0
 */

namespace InnoCraft\Experiments\Variations;

interface VariationInterface {

    /**
     * Get the name of the variation.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the percentage allocated to this variation. Only returns a percentage if a fixed percentage was allocated
     * to this variation. If no percentage is allocated, it will use the default percentage of a variation which depends
     * on the number of set variations within an experiment.
     *
     * @return string
     */
    public function getPercentage();

    /**
     * Runs / executes the given variation. Depending on the variation type a different action may be executed.
     * For example a redirect or calling a callable.
     *
     * @return void
     */
    public function run();

}