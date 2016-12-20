<?php
/**
 * InnoCraft Ltd - We are the makers of Piwik Analytics, the leading open source analytics platform.
 *
 * @link https://www.innocraft.com
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html LGPL v3.0
 */

namespace InnoCraft\Experiments\Filters;

class AlwaysTrigger implements FilterInterface  {

    public function shouldTrigger()
    {
        return true;
    }

}