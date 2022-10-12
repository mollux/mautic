<?php

namespace Mautic\CampaignBundle\Executioner\Result;

use Doctrine\Common\Collections\ArrayCollection;
use Mautic\LeadBundle\Entity\Lead;

class EvaluatedContacts
{
    private \Doctrine\Common\Collections\ArrayCollection $passed;

    private \Doctrine\Common\Collections\ArrayCollection $failed;

    /**
     * EvaluatedContacts constructor.
     */
    public function __construct(ArrayCollection $passed = null, ArrayCollection $failed = null)
    {
        $this->passed = $passed ?? new ArrayCollection();
        $this->failed = $failed ?? new ArrayCollection();
    }

    public function pass(Lead $contact)
    {
        $this->passed->set($contact->getId(), $contact);
    }

    public function fail(Lead $contact)
    {
        $this->failed->set($contact->getId(), $contact);
    }

    /**
     * @return ArrayCollection|Lead[]
     */
    public function getPassed(): \Doctrine\Common\Collections\ArrayCollection|array
    {
        return $this->passed;
    }

    /**
     * @return ArrayCollection|Lead[]
     */
    public function getFailed(): \Doctrine\Common\Collections\ArrayCollection|array
    {
        return $this->failed;
    }
}
