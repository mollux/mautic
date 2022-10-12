<?php

namespace Mautic\LeadBundle\DataObject;

class LeadManipulator
{
    /**
     * If true then the manipulator was logged and should not be logged for the second time.
     */
    private bool $logged = false;

    public function __construct(private ?string $bundleName = null, private ?string $objectName = null, private ?int $objectId = null, private ?string $objectDescription = null)
    {
    }

    /**
     * @return ?string
     */
    public function getBundleName()
    {
        return $this->bundleName;
    }

    /**
     * @return ?string
     */
    public function getObjectName()
    {
        return $this->objectName;
    }

    /**
     * @return ?int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @return ?string
     */
    public function getObjectDescription()
    {
        return $this->objectDescription;
    }

    /**
     * Check if the manipulator was logged already or not.
     *
     * @return bool
     */
    public function wasLogged()
    {
        return $this->logged;
    }

    /**
     * Set manipulator as logged so it wouldn't be logged for the second time in the same request.
     */
    public function setAsLogged()
    {
        $this->logged = true;
    }
}
