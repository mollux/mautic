<?php

namespace Mautic\AssetBundle\Event;

use Gaufrette\Adapter;
use Mautic\CoreBundle\Event\CommonEvent;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Mautic\PluginBundle\Integration\UnifiedIntegrationInterface;

/**
 * Class RemoteAssetBrowseEvent.
 */
class RemoteAssetBrowseEvent extends CommonEvent
{
    private ?\Gaufrette\Adapter $adapter = null;

    public function __construct(private UnifiedIntegrationInterface $integration)
    {
    }

    /**
     * @return Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @return AbstractIntegration
     */
    public function getIntegration()
    {
        return $this->integration;
    }

    /**
     * @return $this
     */
    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }
}
