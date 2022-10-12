<?php

namespace Mautic\CampaignBundle\Executioner\Scheduler\Mode\DAO;

use Doctrine\Common\Collections\ArrayCollection;
use Mautic\LeadBundle\Entity\Lead;

class GroupExecutionDateDAO
{
    private \Doctrine\Common\Collections\ArrayCollection $contacts;

    /**
     * GroupExecutionDateDAO constructor.
     */
    public function __construct(private \DateTime $executionDate)
    {
        $this->contacts      = new ArrayCollection();
    }

    public function addContact(Lead $contact)
    {
        $this->contacts->set($contact->getId(), $contact);
    }

    /**
     * @return \DateTime
     */
    public function getExecutionDate()
    {
        return $this->executionDate;
    }

    /**
     * @return ArrayCollection
     */
    public function getContacts()
    {
        return $this->contacts;
    }
}
