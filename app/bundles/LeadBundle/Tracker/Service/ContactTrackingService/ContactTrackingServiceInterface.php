<?php

namespace Mautic\LeadBundle\Tracker\Service\ContactTrackingService;

use Mautic\LeadBundle\Entity\Lead;

/**
 * Interface ContactTrackingInterface.
 */
interface ContactTrackingServiceInterface
{
    /**
     * Return current tracked Lead.
     *
     * @return Lead|null
     */
    public function getTrackedLead();

    /**
     * @return string Unique identifier
     */
    public function getTrackedIdentifier();
}
