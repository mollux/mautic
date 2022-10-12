<?php

namespace MauticPlugin\MauticSocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

/**
 * Class PostCount.
 */
class PostCount
{
    private ?int $id = null;

    private ?\MauticPlugin\MauticSocialBundle\Entity\Monitoring $monitor = null;

    /**
     * @var \DateTime
     */
    private $postDate;

    private ?int $postCount = null;

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('monitor_post_count')
            ->setCustomRepositoryClass(\MauticPlugin\MauticSocialBundle\Entity\PostCountRepository::class);

        $builder->addId();

        $builder->createManyToOne('monitor', 'Monitoring')
            ->addJoinColumn('monitor_id', 'id', true, false, 'CASCADE')
            ->build();

        $builder->addNamedField('postDate', 'date', 'post_date');

        $builder->addNamedField('postCount', 'integer', 'post_count');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \MauticPlugin\MauticSocialBundle\Entity\Monitoring
     */
    public function getMonitor()
    {
        return $this->monitor;
    }

    /**
     * @param Monitoring $monitor
     *
     * @return $this
     */
    public function setMonitor($monitor)
    {
        $this->monitor = $monitor;

        return $this;
    }

    /**
     * @return int
     */
    public function getPostCount()
    {
        return $this->postCount;
    }

    /**
     * @param int $postCount
     *
     * @return $this
     */
    public function setPostCount($postCount)
    {
        $this->postCount = $postCount;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

    /**
     * @param $postDate
     *
     * @return $this
     */
    public function setPostDate($postDate)
    {
        $this->postDate = $postDate;

        return $this;
    }
}
