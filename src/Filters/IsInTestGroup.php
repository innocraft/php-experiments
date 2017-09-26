<?php
/**
 * InnoCraft Ltd - We are the makers of Piwik Analytics, the leading open source analytics platform.
 *
 * @link https://www.innocraft.com
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html LGPL v3.0
 */

namespace InnoCraft\Experiments\Filters;

use InnoCraft\Experiments\Experiment;
use InnoCraft\Experiments\Storage\StorageInterface;
use InvalidArgumentException;

class IsInTestGroup implements FilterInterface  {

    const MAX_PERCENTAGE = 100;
    const STORAGE_NAMESPACE = 'isInTestGroup';

    /**
     * @var int
     */
    private $percentage;

    /**
     * @var $storage
     */
    private $storage;

    /**
     * @var string
     */
    private $experimentName;

    public function __construct(StorageInterface $storage, $experimentName, $percentage)
    {
        $this->percentage = (int) $percentage;

        if ($this->percentage > self::MAX_PERCENTAGE) {
            throw new InvalidArgumentException('$percentage should not be higher than ' . self::MAX_PERCENTAGE);
        }

        $this->experimentName = $experimentName;
        $this->storage = $storage;
    }

    public function shouldTrigger()
    {
        $isInGroup = $this->storage->get(self::STORAGE_NAMESPACE, $this->experimentName);

        if ($isInGroup !== null) {
            return !empty($isInGroup);
        }

        $isInGroup = Experiment::getRandomInt(1, self::MAX_PERCENTAGE) <= $this->percentage;
        $this->storage->set(self::STORAGE_NAMESPACE, $this->experimentName, $isInGroup ? 1 : 0);

        return $isInGroup;
    }

}