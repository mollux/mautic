<?php

declare(strict_types=1);

namespace Mautic\IntegrationsBundle\Sync\SyncDataExchange\Internal\Executioner;

use Mautic\IntegrationsBundle\Event\InternalObjectCreateEvent;
use Mautic\IntegrationsBundle\Event\InternalObjectUpdateEvent;
use Mautic\IntegrationsBundle\IntegrationEvents;
use Mautic\IntegrationsBundle\Sync\DAO\Sync\Order\OrderDAO;
use Mautic\IntegrationsBundle\Sync\Exception\ObjectNotFoundException;
use Mautic\IntegrationsBundle\Sync\Helper\MappingHelper;
use Mautic\IntegrationsBundle\Sync\Logger\DebugLogger;
use Mautic\IntegrationsBundle\Sync\SyncDataExchange\Internal\ObjectProvider;
use Mautic\IntegrationsBundle\Sync\SyncDataExchange\MauticSyncDataExchange;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OrderExecutioner
{
    public function __construct(private MappingHelper $mappingHelper, private EventDispatcherInterface $dispatcher, private ObjectProvider $objectProvider)
    {
    }

    public function execute(OrderDAO $syncOrderDAO): void
    {
        $identifiedObjects   = $syncOrderDAO->getIdentifiedObjects();
        $unidentifiedObjects = $syncOrderDAO->getUnidentifiedObjects();

        foreach ($identifiedObjects as $objectName => $updateObjects) {
            $this->updateObjects($objectName, $updateObjects, $syncOrderDAO);
        }

        foreach ($unidentifiedObjects as $objectName => $createObjects) {
            $this->createObjects($objectName, $createObjects);
        }
    }

    private function updateObjects(string $objectName, array $updateObjects, OrderDAO $syncOrderDAO): void
    {
        $event = null;
        $updateCount = count($updateObjects);
        DebugLogger::log(
            MauticSyncDataExchange::NAME,
            sprintf(
                'Updating %d %s object(s)',
                $updateCount,
                $objectName
            ),
            self::class.':'.__FUNCTION__
        );

        if (0 === $updateCount) {
            return;
        }

        try {
            $event = new InternalObjectUpdateEvent(
                $this->objectProvider->getObjectByName($objectName),
                $syncOrderDAO->getIdentifiedObjectIds($objectName),
                $updateObjects
            );
        } catch (ObjectNotFoundException) {
            DebugLogger::log(
                MauticSyncDataExchange::NAME,
                $objectName,
                self::class.':'.__FUNCTION__
            );
        }

        $this->dispatcher->dispatch($event, IntegrationEvents::INTEGRATION_UPDATE_INTERNAL_OBJECTS);
        $this->mappingHelper->updateObjectMappings($event->getUpdatedObjectMappings());
    }

    private function createObjects(string $objectName, array $createObjects): void
    {
        $event = null;
        $createCount = count($createObjects);

        DebugLogger::log(
            MauticSyncDataExchange::NAME,
            sprintf(
                'Creating %d %s object(s)',
                $createCount,
                $objectName
            ),
            self::class.':'.__FUNCTION__
        );

        if (0 === $createCount) {
            return;
        }

        try {
            $event = new InternalObjectCreateEvent(
                $this->objectProvider->getObjectByName($objectName),
                $createObjects
            );
        } catch (ObjectNotFoundException) {
            DebugLogger::log(
                MauticSyncDataExchange::NAME,
                $objectName,
                self::class.':'.__FUNCTION__
            );
        }

        $this->dispatcher->dispatch($event, IntegrationEvents::INTEGRATION_CREATE_INTERNAL_OBJECTS);
        $this->mappingHelper->saveObjectMappings($event->getObjectMappings());
    }
}
