<?php

namespace Mautic\CoreBundle\Factory;

use Mautic\CoreBundle\Model\AbstractCommonModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ModelFactory
{
    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * @param $modelNameKey
     *
     * @return AbstractCommonModel
     */
    public function getModel($modelNameKey)
    {
        // Shortcut for models with the same name as the bundle
        if (!str_contains($modelNameKey, '.')) {
            $modelNameKey = "$modelNameKey.$modelNameKey";
        }

        $parts = explode('.', $modelNameKey);

        if (2 !== count($parts)) {
            throw new \InvalidArgumentException($modelNameKey.' is not a valid model key.');
        }

        [$bundle, $name] = $parts;

        // The container is now case sensitive
        $containerKey = sprintf('mautic.%s.model.%s', $bundle, $name);

        if ($this->container->has($containerKey)) {
            return $this->container->get($containerKey);
        }

        throw new \InvalidArgumentException($containerKey.' is not a registered container key.');
    }

    /**
     * Check if a model exists.
     *
     * @param $modelNameKey
     */
    public function hasModel($modelNameKey)
    {
        try {
            $this->getModel($modelNameKey);

            return true;
        } catch (\InvalidArgumentException) {
            return false;
        }
    }
}
