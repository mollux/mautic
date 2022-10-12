<?php

namespace MauticPlugin\MauticSocialBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;
use MauticPlugin\MauticSocialBundle\Entity\Monitoring;

class SocialMonitorEvent extends CommonEvent
{
    /**
     * @var int
     */
    protected $newLeadCount = 0;

    /**
     * @var int
     */
    protected $updatedLeadCount = 0;

    /**
     * @param string $integrationName
     * @param int    $newLeadCount
     * @param int    $updatedLeadCount
     */
    public function __construct(protected $integrationName, Monitoring $monitoring, protected array $leadIds, $newLeadCount, $updatedLeadCount)
    {
        $this->entity           = $monitoring;
        $this->newLeadCount     = (int) $newLeadCount;
        $this->updatedLeadCount = (int) $updatedLeadCount;
    }

    /**
     * Returns the Monitoring entity.
     *
     * @return Monitoring
     */
    public function getMonitoring()
    {
        return $this->entity;
    }

    /**
     * Get count of new leads.
     *
     * @return int
     */
    public function getNewLeadCount()
    {
        return $this->newLeadCount;
    }

    /**
     * Get count of updated leads.
     *
     * @return int
     */
    public function getUpdatedLeadCount()
    {
        return $this->updatedLeadCount;
    }

    public function getTotalLeadCount(): array|int
    {
        return $this->updatedLeadCount + $this->newLeadCount;
    }

    /**
     * @return array
     */
    public function getLeadIds()
    {
        return $this->leadIds;
    }

    /**
     * @return mixed
     */
    public function getIntegrationName()
    {
        return $this->integrationName;
    }
}
