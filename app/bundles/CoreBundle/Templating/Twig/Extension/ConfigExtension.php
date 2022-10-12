<?php

declare(strict_types=1);

namespace Mautic\CoreBundle\Templating\Twig\Extension;

use Mautic\CoreBundle\Templating\Helper\ConfigHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConfigExtension extends AbstractExtension
{
    public function __construct(private ConfigHelper $configHelper)
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('configGetParameter', [$this, 'get']),
        ];
    }

    /**
     * @return mixed
     */
    public function get(string $name, mixed $default = null)
    {
        return $this->configHelper->get($name, $default);
    }
}
