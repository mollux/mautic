<?php

namespace Mautic\UserBundle\Event;

use Mautic\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class LogoutEvent.
 */
class LogoutEvent extends Event
{
    private array $session = [];

    /**
     * LogoutEvent constructor.
     */
    public function __construct(private User $user, private Request $request)
    {
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add value to session after it's been cleared.
     *
     * @param $key
     * @param $value
     */
    public function setPostSessionItem($key, $value)
    {
        $this->session[$key] = $value;
    }

    /**
     * Get session items to be added after session has been cleared.
     *
     * @return array
     */
    public function getPostSessionItems()
    {
        return $this->session;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
