<?php

namespace Mautic\EmailBundle\Swiftmailer\Transport;

use Mautic\EmailBundle\Swiftmailer\Spool\DelegatingSpool;

class SpoolTransport extends \Swift_Transport_SpoolTransport
{
    /**
     * SpoolTransport constructor.
     */
    public function __construct(private \Swift_Events_EventDispatcher $eventDispatcher, private DelegatingSpool $spool)
    {
        parent::__construct($eventDispatcher, $spool);
    }

    /**
     * Sends or queues the given message.
     *
     * @param string[] $failedRecipients An array of failures by-reference
     *
     * @return int The number of sent e-mails
     *
     * @throws \Swift_IoException
     */
    public function send(\Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        if ($evt = $this->eventDispatcher->createSendEvent($this, $message)) {
            $this->eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
            if ($evt->bubbleCancelled()) {
                return 0;
            }
        }

        $count = $this->spool->delegateMessage($message, $failedRecipients);

        if ($evt) {
            $successResult = $this->spool->wasMessageSpooled() ? \Swift_Events_SendEvent::RESULT_SPOOLED : \Swift_Events_SendEvent::RESULT_SUCCESS;
            $evt->setResult($count ? $successResult : \Swift_Events_SendEvent::RESULT_FAILED);
            $this->eventDispatcher->dispatchEvent($evt, 'sendPerformed');
        }

        return $count;
    }

    public function supportsTokenization(): bool
    {
        return $this->spool->isTokenizationEnabled();
    }

    public function getMaxBatchLimit()
    {
        return $this->spool->getRealTransport()->getMaxBatchLimit();
    }

    public function getBatchRecipientCount(\Swift_Message $message, $toBeAdded = 1, $type = 'to')
    {
        return $this->spool->getRealTransport()->getBatchRecipientCount($message, $toBeAdded, $type);
    }
}
