<?php

namespace Mautic\UserBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class AuthenticationContentEvent.
 */
class AuthenticationContentEvent extends Event
{
    /**
     * @var array
     */
    protected $content = [];

    /**
     * @var bool
     */
    protected $postLogout = false;

    /**
     * AuthenticationContentEvent constructor.
     */
    public function __construct(protected Request $request)
    {
        $this->postLogout = $request->getSession()->get('post_logout', false);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return bool
     */
    public function isLogout()
    {
        return $this->postLogout;
    }

    /**
     * @param $content
     */
    public function addContent($content)
    {
        $this->content[] = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return implode("\n\n", $this->content);
    }
}
