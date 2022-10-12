<?php

namespace Mautic\ReportBundle\Scheduler\Entity;

use Mautic\ReportBundle\Scheduler\Enum\SchedulerEnum;
use Mautic\ReportBundle\Scheduler\SchedulerInterface;

class SchedulerEntity implements SchedulerInterface
{
    /**
     * @param bool $isScheduled
     */
    public function __construct(private $isScheduled, private ?string $scheduleUnit, private ?string $scheduleDay, private ?string $scheduleMonthFrequency)
    {
    }

    /**
     * @return bool
     */
    public function isScheduled()
    {
        return $this->isScheduled;
    }

    /**
     * @return string|null
     */
    public function getScheduleUnit()
    {
        return $this->scheduleUnit;
    }

    /**
     * @return string|null
     */
    public function getScheduleDay()
    {
        return $this->scheduleDay;
    }

    /**
     * @return string|null
     */
    public function getScheduleMonthFrequency()
    {
        return $this->scheduleMonthFrequency;
    }

    public function isScheduledNow(): bool
    {
        return SchedulerEnum::UNIT_NOW === $this->getScheduleUnit();
    }

    public function isScheduledDaily()
    {
        return SchedulerEnum::UNIT_DAILY === $this->getScheduleUnit();
    }

    public function isScheduledWeekly()
    {
        return SchedulerEnum::UNIT_WEEKLY === $this->getScheduleUnit();
    }

    public function isScheduledMonthly()
    {
        return SchedulerEnum::UNIT_MONTHLY === $this->getScheduleUnit();
    }

    public function isScheduledWeekDays()
    {
        return SchedulerEnum::DAY_WEEK_DAYS === $this->getScheduleDay();
    }
}
