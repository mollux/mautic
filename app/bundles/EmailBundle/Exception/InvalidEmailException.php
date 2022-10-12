<?php

namespace Mautic\EmailBundle\Exception;

use Mautic\CoreBundle\Exception\InvalidValueException;
use Throwable;

class InvalidEmailException extends InvalidValueException
{
    /**
     * @param string $emailAddress
     * @param string $message
     * @param int    $code
     */
    public function __construct(protected $emailAddress, $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }
}
