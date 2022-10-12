<?php

namespace Mautic\EmailBundle\Entity;

use Mautic\LeadBundle\Entity\Lead;

/**
 * Interface EmailReplyRepositoryInterface.
 */
interface EmailReplyRepositoryInterface
{
    /**
     * @param array    $options
     * @return array
     */
    public function getByLeadIdForTimeline(int|\Mautic\LeadBundle\Entity\Lead $leadId, $options);
}
