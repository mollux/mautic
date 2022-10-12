<?php

declare(strict_types=1);

namespace Mautic\IntegrationsBundle\Sync\Notification\Helper;

use Mautic\IntegrationsBundle\Sync\Notification\Writer;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserNotificationHelper
{
    private ?string $integrationDisplayName = null;

    private ?string $objectDisplayName = null;

    public function __construct(private Writer $writer, private UserHelper $userHelper, private OwnerProvider $ownerProvider, private RouteHelper $routeHelper, private TranslatorInterface $translator)
    {
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Mautic\IntegrationsBundle\Sync\Exception\ObjectNotSupportedException
     */
    public function writeNotification(
        string $message,
        string $integrationDisplayName,
        string $objectDisplayName,
        string $mauticObject,
        int $id,
        string $linkText
    ): void {
        $this->integrationDisplayName = $integrationDisplayName;
        $this->objectDisplayName      = $objectDisplayName;
        $link                         = $this->routeHelper->getLink($mauticObject, $id, $linkText);
        $owners                       = $this->ownerProvider->getOwnersForObjectIds($mauticObject, [$id]);

        if (!empty($owners[0]['owner_id'])) {
            $this->writeMessage($message, $link, (int) $owners[0]['owner_id']);

            return;
        }

        $adminUsers = $this->userHelper->getAdminUsers();
        foreach ($adminUsers as $userId) {
            $this->writeMessage($message, $link, $userId);
        }
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    private function writeMessage(string $message, string $link, int $userId): void
    {
        $this->writer->writeUserNotification(
            $this->translator->trans(
                'mautic.integration.sync.user_notification.header',
                [
                    '%integration%' => $this->integrationDisplayName,
                    '%object%'      => $this->objectDisplayName,
                ]
            ),
            $this->translator->trans(
                'mautic.integration.sync.user_notification.sync_error',
                [
                    '%name%'    => $link,
                    '%message%' => $message,
                ]
            ),
            $userId
        );
    }
}
