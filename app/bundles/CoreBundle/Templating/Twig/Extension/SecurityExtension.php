<?php

declare(strict_types=1);

namespace Mautic\CoreBundle\Templating\Twig\Extension;

use Mautic\CoreBundle\Templating\Helper\SecurityHelper;
use Mautic\UserBundle\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SecurityExtension extends AbstractExtension
{
    public function __construct(private SecurityHelper $securityHelper)
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('securityGetAuthenticationContext', [$this, 'getContext']),
            new TwigFunction('securityGetCsrfToken', [$this, 'getCsrfToken']),
            new TwigFunction('securityHasEntityAccess', [$this, 'hasEntityAccess']),
        ];
    }

    public function getContext(): string
    {
        return $this->securityHelper->getAuthenticationContent();
    }

    public function getCsrfToken(string $intention): string
    {
        return $this->securityHelper->getCsrfToken($intention);
    }

    /**
     * Helper function to check if the logged in user has access to an entity.
     */
    public function hasEntityAccess(string|bool $ownPermission, string|bool $otherPermission, \Mautic\UserBundle\Entity\User|int $ownerId): bool
    {
        return $this->securityHelper->hasEntityAccess($ownPermission, $otherPermission, $ownerId);
    }
}
