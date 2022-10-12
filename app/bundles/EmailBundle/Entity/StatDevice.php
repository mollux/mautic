<?php

namespace Mautic\EmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\IpAddress;
use Mautic\LeadBundle\Entity\LeadDevice;

/**
 * Class StatDevice.
 */
class StatDevice
{
    private ?int $id = null;

    /**
     * @var array
     */
    private $stat;

    private ?\Mautic\LeadBundle\Entity\LeadDevice $device = null;

    private ?\Mautic\CoreBundle\Entity\IpAddress $ipAddress = null;

    private ?\DateTime $dateOpened = null;

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('email_stats_devices')
            ->setCustomRepositoryClass(\Mautic\EmailBundle\Entity\StatDeviceRepository::class)
            ->addIndex(['date_opened'], 'date_opened_search');

        $builder->addBigIntIdField();

        $builder->createManyToOne('device', \Mautic\LeadBundle\Entity\LeadDevice::class)
            ->addJoinColumn('device_id', 'id', true, false, 'CASCADE')
            ->build();

        $builder->createManyToOne('stat', 'Stat')
            ->addJoinColumn('stat_id', 'id', true, false, 'CASCADE')
            ->build();

        $builder->addIpAddress(true);

        $builder->createField('dateOpened', 'datetime')
            ->columnName('date_opened')
            ->build();
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
                    'device',
                    'ipAddress',
                    'stat',
                ]
            )
            ->build();
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
     * @return Stat
     */
    public function getStat()
    {
        return $this->stat;
    }

    /**
     * @param Stat
     */
    public function setStat(Stat $stat)
    {
        $this->stat = $stat;
    }

    /**
     * @return mixed
     */
    public function getDateOpened()
    {
        return $this->dateOpened;
    }

    public function setDateOpened(mixed $dateOpened)
    {
        $this->dateOpened = $dateOpened;
    }

    /**
     * @return mixed
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param mixed $device
     */
    public function setDevice(LeadDevice $device)
    {
        $this->device = $device;
    }
}
