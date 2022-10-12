<?php

namespace Mautic\EmailBundle\MonitoredEmail\Processor\Bounce\Mapper;

/**
 * Class Category.
 */
class Category
{
    /**
     * Category constructor.
     *
     * @param $category
     * @param $type
     * @param $isPermanent
     * @param string $category
     * @param string $type
     * @param bool $isPermanent
     */
    public function __construct(private $category, private $type, private $isPermanent)
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isPermanent()
    {
        return $this->isPermanent;
    }
}
