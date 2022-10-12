<?php

namespace Mautic\PageBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class UntrackableUrlsEvent.
 */
class UntrackableUrlsEvent extends Event
{
    private array $doNotTrack = [
        '{webview_url}',
        '{unsubscribe_url}',
        '{trackable=(.*?)}',
    ];

    /**
     * TrackableEvent constructor.
     *
     * @param $content
     * @param string $content
     */
    public function __construct(private $content)
    {
    }

    /**
     * set a URL or token to not convert to trackables.
     *
     * @param $url
     */
    public function addNonTrackable($url)
    {
        $this->doNotTrack[] = $url;
    }

    /**
     * Get array of non-trackables.
     *
     * @return array
     */
    public function getDoNotTrackList()
    {
        return $this->doNotTrack;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
