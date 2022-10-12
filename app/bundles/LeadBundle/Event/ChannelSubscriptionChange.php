<?php

namespace Mautic\LeadBundle\Event;

use Mautic\LeadBundle\Entity\DoNotContact;
use Mautic\LeadBundle\Entity\Lead;
use Symfony\Contracts\EventDispatcher\Event;

class ChannelSubscriptionChange extends Event
{
    /**
     * ContactStatusChange constructor.
     *
     * @param $channel
     * @param $oldStatus
     * @param $newStatus
     * @param string $channel
     * @param string $oldStatus
     * @param string $newStatus
     */
    public function __construct(private Lead $lead, private $channel, private $oldStatus, private $newStatus)
    {
    }

    /**
     * @return Lead
     */
    public function getLead()
    {
        return $this->lead;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return int
     */
    public function getOldStatus()
    {
        return $this->oldStatus;
    }

    /**
     * @return string
     */
    public function getOldStatusVerb()
    {
        return $this->getDncReasonVerb($this->oldStatus);
    }

    /**
     * @return int
     */
    public function getNewStatus()
    {
        return $this->newStatus;
    }

    /**
     * @return string
     */
    public function getNewStatusVerb()
    {
        return $this->getDncReasonVerb($this->newStatus);
    }

    /**
     * @param $reason
     *
     * @return string
     */
    private function getDncReasonVerb($reason)
    {
        return match (true) {
            DoNotContact::IS_CONTACTABLE === $reason => 'contactable',
            DoNotContact::BOUNCED === $reason => 'bounced',
            DoNotContact::MANUAL === $reason => 'manual',
            default => 'unsubscribed',
        };
    }
}
