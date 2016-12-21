<?php
/**
 * InnoCraft Ltd - We are the makers of Piwik Analytics, the leading open source analytics platform.
 *
 * @link https://www.innocraft.com
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html LGPL v3.0
 */

namespace InnoCraft\Experiments\Filters;

use DateTimeInterface;
use DateTime;

class ScheduledDate implements FilterInterface  {

    /**
     * @var DateTimeInterface
     */
    private $now;

    /**
     * @var DateTimeInterface
     */
    private $startDate;

    /**
     * @var DateTimeInterface
     */
    private $endDate;

    /**
     * ScheduledDate constructor.
     *
     * When passing a string:
     * Date values separated by slash are assumed to be in American order: m/d/y
     * Date values separated by dash are assumed to be in European order: d-m-y
     *
     * @param string|DateTimeInterface $now
     * @param null|string|DateTimeInterface $startDate  null if no start date is given and it is valid from any time
     * @param null|string|DateTimeInterface $endDate    null if no end date is given and it is valid "unlimited"
     */
    public function __construct($now, $startDate, $endDate)
    {
        if ($now instanceof DateTimeInterface) {
            $this->now = $now;
        } elseif ($now) {
            $this->now = new DateTime($now);
        } else {
            $this->now = new DateTime();
        }

        if ($startDate instanceof DateTimeInterface) {
            $this->startDate = $startDate;
        } elseif ($startDate !== null) {
            $this->startDate = new DateTime($startDate);
        }

        if ($endDate instanceof DateTimeInterface) {
            $this->endDate = $endDate;
        } elseif ($endDate !== null) {
            $this->endDate = new DateTime($endDate);
        }
    }

    public function shouldTrigger()
    {
        if ($this->startDate && $this->startDate > $this->now) {
            return false;
        }

        if ($this->endDate && $this->endDate < $this->now) {
            return false;
        }

        return true;
    }

}