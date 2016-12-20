<?php
/**
 * InnoCraft Ltd - We are the makers of Piwik Analytics, the leading open source analytics platform.
 *
 * @link https://www.innocraft.com
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html LGPL v3.0
 */

namespace InnoCraft\Experiments\Filters;

use InnoCraft\Experiments\Storage\StorageInterface;

class DefaultFilters implements FilterInterface {

    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * @param string|int $experimentName
     * @param StorageInterface $storage
     * @param array $config Can be empty if no default filters should be added
     */
    public function __construct($experimentName, StorageInterface $storage, $config)
    {
        $percentage = $this->getValueFromConfig($config, 'percentage', 100);
        $this->filters[] = new IsInTestGroup($storage, $experimentName, $percentage);

        $startDate = $this->getValueFromConfig($config, 'startDate', null);
        $endDate = $this->getValueFromConfig($config, 'endDate', null);
        $now = $this->getValueFromConfig($config, 'currentDate', 'now');
        $this->filters[] = new ScheduledDate($now, $startDate, $endDate);

        if (isset($config['customFilter']) && $config['customFilter'] instanceof FilterInterface) {
            $this->filters[] = $config['customFilter'];
        } elseif (isset($config['customFilter'])) {
            $this->filters[] = new CustomFilter($config['customFilter']);
        }
    }

    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    private function getValueFromConfig($config, $field, $default)
    {
        if (isset($config[$field])) {
            return $config[$field];
        }

        return $default;
    }

    public function shouldTrigger()
    {
        foreach ($this->filters as $filter) {
            if (!$filter->shouldTrigger()) {
                return false;
            }
        }

        return true;
    }

}