<?php
/**
 * InnoCraft Ltd - We are the makers of Piwik Analytics, the leading open source analytics platform.
 *
 * @link https://www.innocraft.com
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html LGPL v3.0
 */

namespace InnoCraft\Experiments\Filters;

use InvalidArgumentException;

class CustomFilter implements FilterInterface  {

    /**
     * @var \Callable
     */
    private $callback;

    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('$callback is not a callable');
        }

        $this->callback = $callback;
    }

    public function shouldTrigger()
    {
        return (bool) call_user_func($this->callback);
    }

}