<?php

namespace Mautic\QueueBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;

/**
 * Class QueueEvent.
 */
class QueueEvent extends CommonEvent
{
    /**
     * QueueEvent constructor.
     *
     * @param string   $protocol
     * @param string   $queueName
     */
    public function __construct(private $protocol, private $queueName, private array $payload = [], private ?int $messages = null, private ?int $timeout = null)
    {
    }

    /**
     * @return int|null
     */
    public function getMessages()
    {
        return $this->messages;
    }

    public function getPayload($returnArray = false): string|array
    {
        return ($returnArray) ? $this->payload : json_encode($this->payload, JSON_THROW_ON_ERROR);
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return string
     */
    public function getQueueName()
    {
        return $this->queueName;
    }

    /**
     * @return int|null
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param string $protocol
     *
     * @return bool
     */
    public function checkContext($protocol)
    {
        return $protocol == $this->protocol;
    }
}
