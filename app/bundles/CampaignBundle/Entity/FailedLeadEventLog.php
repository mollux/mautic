<?php

namespace Mautic\CampaignBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

/**
 * Class LeadEventLog.
 */
class FailedLeadEventLog
{
    private ?\Mautic\CampaignBundle\Entity\LeadEventLog $log = null;

    private ?\DateTime $dateAdded = null;

    private ?string $reason = null;

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('campaign_lead_event_failed_log')
            ->setCustomRepositoryClass(FailedLeadEventLogRepository::class)
            ->addIndex(['date_added'], 'campaign_event_failed_date');

        $builder->createOneToOne('log', 'LeadEventLog')
            ->makePrimaryKey()
            ->inversedBy('failedLog')
            ->addJoinColumn('log_id', 'id', false, false, 'CASCADE')
            ->build();

        $builder->addDateAdded();

        $builder->addNullableField('reason', 'text');
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('campaignEventFailedLog')
                 ->addProperties(
                     [
                         'dateAdded',
                         'reason',
                     ]
                 )
                 ->build();
    }

    /**
     * @return LeadEventLog
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @return FailedLeadEventLog
     */
    public function setLog(LeadEventLog $log = null)
    {
        $this->log = $log;

        if ($log) {
            $log->setFailedLog($this);
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @return FailedLeadEventLog
     */
    public function setDateAdded(\DateTime $dateAdded = null)
    {
        if (null === $dateAdded) {
            $dateAdded = new \DateTime();
        }

        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     *
     * @return FailedLeadEventLog
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }
}
