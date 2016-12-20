<?php
/**
 * InnoCraft Ltd - We are the makers of Piwik Analytics, the leading open source analytics platform.
 *
 * @link https://www.innocraft.com
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html LGPL v3.0
 */

namespace InnoCraft\Experiments\Variations;

class CallableVariation extends StandardVariation {

    /**
     * Returns a callable if the option was passed.
     * @return callable
     */
    public function getCallable()
    {
        if (isset($this->variation['callable'])) {
            return $this->variation['callable'];
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $callable = $this->getCallable();

        if (is_callable($callable)) {
            call_user_func($callable, $this);
        }
    }

}