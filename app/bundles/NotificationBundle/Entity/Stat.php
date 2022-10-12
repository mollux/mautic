<?php

namespace Mautic\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\IpAddress;
use Mautic\LeadBundle\Entity\Lead;

/**
 * Class Stat.
 */
class Stat
{
    private ?int $id = null;

    private ?\Mautic\NotificationBundle\Entity\Notification $notification = null;

    private ?\Mautic\LeadBundle\Entity\Lead $lead = null;

    private ?\Mautic\LeadBundle\Entity\LeadList $list = null;

    private ?\Mautic\CoreBundle\Entity\IpAddress $ipAddress = null;

    private ?\DateTime $dateSent = null;

    private ?\DateTime $dateRead = null;

    private bool $isClicked = false;

    private ?\DateTime $dateClicked = null;

    private ?string $trackingHash = null;

    private int $retryCount = 0;

    private ?string $source = null;

    private ?int $sourceId = null;

    private array $tokens = [];

    private ?int $clickCount = null;

    private array $clickDetails = [];

    private ?\DateTime $lastClicked = null;

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('push_notification_stats')
            ->setCustomRepositoryClass(\Mautic\NotificationBundle\Entity\StatRepository::class)
            ->addIndex(['notification_id', 'lead_id'], 'stat_notification_search')
            ->addIndex(['is_clicked'], 'stat_notification_clicked_search')
            ->addIndex(['tracking_hash'], 'stat_notification_hash_search')
            ->addIndex(['source', 'source_id'], 'stat_notification_source_search');

        $builder->addBigIntIdField();

        $builder->createManyToOne('notification', 'Notification')
            ->inversedBy('stats')
            ->addJoinColumn('notification_id', 'id', true, false, 'SET NULL')
            ->build();

        $builder->addLead(true, 'SET NULL');

        $builder->createManyToOne('list', \Mautic\LeadBundle\Entity\LeadList::class)
            ->addJoinColumn('list_id', 'id', true, false, 'SET NULL')
            ->build();

        $builder->addIpAddress(true);

        $builder->createField('dateSent', 'datetime')
            ->columnName('date_sent')
            ->build();

        $builder->createField('dateRead', 'datetime')
            ->columnName('date_read')
            ->nullable()
            ->build();

        $builder->createField('isClicked', 'boolean')
            ->columnName('is_clicked')
            ->build();

        $builder->createField('dateClicked', 'datetime')
            ->columnName('date_clicked')
            ->nullable()
            ->build();

        $builder->createField('trackingHash', 'string')
            ->columnName('tracking_hash')
            ->nullable()
            ->build();

        $builder->createField('retryCount', 'integer')
            ->columnName('retry_count')
            ->nullable()
            ->build();

        $builder->createField('source', 'string')
            ->nullable()
            ->build();

        $builder->createField('sourceId', 'integer')
            ->columnName('source_id')
            ->nullable()
            ->build();

        $builder->createField('tokens', 'array')
            ->nullable()
            ->build();

        $builder->addNullableField('clickCount', 'integer', 'click_count');

        $builder->addNullableField('lastClicked', 'datetime', 'last_clicked');

        $builder->addNullableField('clickDetails', 'array', 'click_details');
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('stat')
            ->addProperties(
                [
                    'id',
                    'ipAddress',
                    'dateSent',
                    'isClicked',
                    'dateClicked',
                    'retryCount',
                    'source',
                    'clickCount',
                    'lastClicked',
                    'sourceId',
                    'trackingHash',
                    'lead',
                    'notification',
                ]
            )
            ->build();
    }

    /**
     * @return mixed
     */
    public function getDateClicked()
    {
        return $this->dateClicked;
    }

    public function setDateClicked(mixed $dateClicked)
    {
        $this->dateClicked = $dateClicked;
    }

    /**
     * @return mixed
     */
    public function getDateSent()
    {
        return $this->dateSent;
    }

    public function setDateSent(mixed $dateSent)
    {
        $this->dateSent = $dateSent;
    }

    /**
     * @return Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    public function setNotification(Notification $notification = null)
    {
        $this->notification = $notification;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return IpAddress
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param mixed $ip
     */
    public function setIpAddress(IpAddress $ip)
    {
        $this->ipAddress = $ip;
    }

    /**
     * @return mixed
     */
    public function getIsClicked()
    {
        return $this->isClicked;
    }

    public function setIsClicked(mixed $isClicked)
    {
        $this->isClicked = $isClicked;
    }

    /**
     * @return Lead
     */
    public function getLead()
    {
        return $this->lead;
    }

    /**
     * @param mixed $lead
     */
    public function setLead(Lead $lead = null)
    {
        $this->lead = $lead;
    }

    /**
     * @return mixed
     */
    public function getTrackingHash()
    {
        return $this->trackingHash;
    }

    public function setTrackingHash(mixed $trackingHash)
    {
        $this->trackingHash = $trackingHash;
    }

    /**
     * @return \Mautic\LeadBundle\Entity\LeadList
     */
    public function getList()
    {
        return $this->list;
    }

    public function setList(mixed $list)
    {
        $this->list = $list;
    }

    /**
     * @return mixed
     */
    public function getRetryCount()
    {
        return $this->retryCount;
    }

    public function setRetryCount(mixed $retryCount)
    {
        $this->retryCount = $retryCount;
    }

    public function upRetryCount()
    {
        ++$this->retryCount;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    public function setSource(mixed $source)
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    public function setSourceId(mixed $sourceId)
    {
        $this->sourceId = (int) $sourceId;
    }

    /**
     * @return mixed
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    public function setTokens(mixed $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @return mixed
     */
    public function getClickCount()
    {
        return $this->clickCount;
    }

    /**
     * @return Stat
     */
    public function setClickCount(mixed $clickCount)
    {
        $this->clickCount = $clickCount;

        return $this;
    }

    /**
     * @param $details
     */
    public function addClickDetails($details)
    {
        $this->clickDetails[] = $details;

        ++$this->clickCount;
    }

    /**
     * Up the sent count.
     *
     * @return Stat
     */
    public function upClickCount()
    {
        $count            = (int) $this->clickCount + 1;
        $this->clickCount = $count;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastClicked()
    {
        return $this->lastClicked;
    }

    /**
     * @return Stat
     */
    public function setLastClicked(\DateTime $lastClicked)
    {
        $this->lastClicked = $lastClicked;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClickDetails()
    {
        return $this->clickDetails;
    }

    /**
     * @return Stat
     */
    public function setClickDetails(mixed $clickDetails)
    {
        $this->clickDetails = $clickDetails;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateRead()
    {
        return $this->dateRead;
    }

    /**
     * @param \DateTime $dateRead
     *
     * @return Stat
     */
    public function setDateRead($dateRead)
    {
        $this->dateRead = $dateRead;

        return $this;
    }
}
