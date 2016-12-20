<?php
/**
 * InnoCraft Ltd - We are the makers of Piwik Analytics, the leading open source analytics platform.
 *
 * @link https://www.innocraft.com
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html LGPL v3.0
 */

namespace InnoCraft\Experiments\Storage;

interface StorageInterface {

    /**
     * @param string $namespace
     * @param string $key
     * @return string|int
     */
    public function get($namespace, $key);

    /**
     * @param string $namespace
     * @param string $key
     * @param string|int $value
     */
    public function set($namespace, $key, $value);

}