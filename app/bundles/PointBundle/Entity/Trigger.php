<?php

namespace Mautic\PointBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Trigger extends FormEntity
{
    private ?int $id = null;

    private ?string $name = null;

    private ?string $description = null;

    private ?\DateTime $publishUp = null;

    private ?\DateTime $publishDown = null;

    private int $points = 0;

    private string $color = 'a0acb8';

    private bool $triggerExistingLeads = false;

    private ?\Mautic\CategoryBundle\Entity\Category $category = null;

    private \Doctrine\Common\Collections\ArrayCollection $events;

    public function __clone()
    {
        $this->id = null;

        parent::__clone();
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('point_triggers')
            ->setCustomRepositoryClass(\Mautic\PointBundle\Entity\TriggerRepository::class);

        $builder->addIdColumns();

        $builder->addPublishDates();

        $builder->addField('points', 'integer');

        $builder->createField('color', 'string')
            ->length(7)
            ->build();

        $builder->createField('triggerExistingLeads', 'boolean')
            ->columnName('trigger_existing_leads')
            ->build();

        $builder->addCategory();

        $builder->createOneToMany('events', 'TriggerEvent')
            ->setIndexBy('id')
            ->setOrderBy(['order' => 'ASC'])
            ->mappedBy('trigger')
            ->cascadeAll()
            ->fetchExtraLazy()
            ->build();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank([
            'message' => 'mautic.core.name.required',
        ]));
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('trigger')
            ->addListProperties(
                [
                    'id',
                    'name',
                    'category',
                    'description',
                ]
            )
            ->addProperties(
                [
                    'publishUp',
                    'publishDown',
                    'points',
                    'color',
                    'events',
                    'triggerExistingLeads',
                ]
            )
            ->build();
    }

    /**
     * @param string $prop
     * @param mixed  $val
     */
    protected function isChanged($prop, $val)
    {
        if ('events' == $prop) {
            //changes are already computed so just add them
            $this->changes[$prop][$val[0]] = $val[1];
        } else {
            parent::isChanged($prop, $val);
        }
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Trigger
     */
    public function setDescription($description)
    {
        $this->isChanged('description', $description);
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Trigger
     */
    public function setName($name)
    {
        $this->isChanged('name', $name);
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add events.
     *
     * @param $key
     *
     * @return Point
     */
    public function addTriggerEvent($key, TriggerEvent $event)
    {
        if ($changes = $event->getChanges()) {
            $this->isChanged('events', [$key, $changes]);
        }
        $this->events[$key] = $event;

        return $this;
    }

    /**
     * Remove events.
     */
    public function removeTriggerEvent(TriggerEvent $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * Get events.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set publishUp.
     *
     * @param \DateTime $publishUp
     *
     * @return Point
     */
    public function setPublishUp($publishUp)
    {
        $this->isChanged('publishUp', $publishUp);
        $this->publishUp = $publishUp;

        return $this;
    }

    /**
     * Get publishUp.
     *
     * @return \DateTime
     */
    public function getPublishUp()
    {
        return $this->publishUp;
    }

    /**
     * Set publishDown.
     *
     * @param \DateTime $publishDown
     *
     * @return Point
     */
    public function setPublishDown($publishDown)
    {
        $this->isChanged('publishDown', $publishDown);
        $this->publishDown = $publishDown;

        return $this;
    }

    /**
     * Get publishDown.
     *
     * @return \DateTime
     */
    public function getPublishDown()
    {
        return $this->publishDown;
    }

    /**
     * @return mixed
     */
    public function getPoints()
    {
        return $this->points;
    }

    public function setPoints(mixed $points)
    {
        $this->isChanged('points', $points);
        $this->points = $points;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    public function setColor(mixed $color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getTriggerExistingLeads()
    {
        return $this->triggerExistingLeads;
    }

    public function setTriggerExistingLeads(mixed $triggerExistingLeads)
    {
        $this->triggerExistingLeads = $triggerExistingLeads;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(mixed $category)
    {
        $this->category = $category;
    }
}
