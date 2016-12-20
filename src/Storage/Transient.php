<?php
/**
 * InnoCraft Ltd - We are the makers of Piwik Analytics, the leading open source analytics platform.
 *
 * @link https://www.innocraft.com
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html LGPL v3.0
 */

namespace InnoCraft\Experiments\Storage;

/**
 * Only meant for development in tests. If you wanted to use this for some reason in production,
 * $data should be static in case the same experiment is created several times during one http requests
 */
class Transient implements StorageInterface {

    private $data = [];

    public function get($namespace, $key)
    {
        $name = $this->toName($namespace, $key);

        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
    }

    public function set($namespace, $key, $value)
    {
        $name = $this->toName($namespace, $key);

        $this->data[$name] = $value;
    }

    private function toName($namespace, $key)
    {
        return $namespace . '_' . $key;
    }

}