<?php

namespace Mautic\StageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

/**
 * Class LeadStageLog.
 */
class LeadStageLog
{
    private ?\Mautic\StageBundle\Entity\Stage $stage = null;

    private ?\Mautic\LeadBundle\Entity\Lead $lead = null;

    private ?\Mautic\CoreBundle\Entity\IpAddress $ipAddress = null;

    private ?\DateTime $dateFired = null;

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('stage_lead_action_log')
            ->setCustomRepositoryClass(\Mautic\StageBundle\Entity\LeadStageLogRepository::class);

        $builder->createManyToOne('stage', 'Stage')
            ->isPrimaryKey()
            ->addJoinColumn('stage_id', 'id', true, false, 'CASCADE')
            ->inversedBy('log')
            ->build();

        $builder->addLead(false, 'CASCADE', true);

        $builder->addIpAddress(true);

        $builder->createField('dateFired', 'datetime')
            ->columnName('date_fired')
            ->build();
    }

    /**
     * @return mixed
     */
    public function getDateFired()
    {
        return $this->dateFired;
    }

    public function setDateFired(mixed $dateFired)
    {
        $this->dateFired = $dateFired;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    public function setIpAddress(mixed $ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return mixed
     */
    public function getLead()
    {
        return $this->lead;
    }

    public function setLead(mixed $lead)
    {
        $this->lead = $lead;
    }

    /**
     * @return mixed
     */
    public function getStage()
    {
        return $this->stage;
    }

    public function setStage(mixed $stage)
    {
        $this->stage = $stage;
    }
}
