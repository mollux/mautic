<?php

namespace Mautic\CoreBundle\Factory;

use GuzzleHttp\Client;
use Mautic\CoreBundle\IpLookup\AbstractLookup;
use Psr\Log\LoggerInterface;

class IpLookupFactory
{
    public function __construct(protected array $lookupServices, protected ?\Psr\Log\LoggerInterface $logger = null, protected ?\GuzzleHttp\Client $client = null, protected ?string $cacheDir = null)
    {
    }

    /**
     * @param      $service
     * @param null $auth
     *
     * @return AbstractLookup|null
     */
    public function getService($service, $auth = null, array $ipLookupConfig = [])
    {
        static $services = [];

        if (empty($service)) {
            return null;
        }

        if (!isset($services[$service]) || (null !== $auth || null !== $ipLookupConfig)) {
            if (!isset($this->lookupServices[$service])) {
                throw new \InvalidArgumentException($service.' not registered.');
            }

            $className = $this->lookupServices[$service]['class'];
            if (!str_starts_with($className, '\\')) {
                $className = '\\'.$className;
            }

            $services[$service] = new $className(
                $auth,
                $ipLookupConfig,
                $this->cacheDir,
                $this->logger,
                $this->client
            );
        }

        return $services[$service];
    }
}
