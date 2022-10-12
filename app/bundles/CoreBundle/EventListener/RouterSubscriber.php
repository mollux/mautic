<?php

namespace Mautic\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class RouterSubscriber implements EventSubscriberInterface
{
    /**
     * @var string|null
     */
    private $httpsPort;

    /**
     * @var string|null
     */
    private $httpPort;

    /**
     * @param string|null $httpsPort
     * @param string|null $httpPort
     */
    public function __construct(private RouterInterface $router, private ?string $scheme, private ?string $host, $httpsPort, $httpPort, private ?string $baseUrl)
    {
        $this->httpsPort = $httpsPort ?? 443;
        $this->httpPort  = $httpPort ?? 80;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['setRouterRequestContext', 1],
        ];
    }

    /**
     * This forces generated routes to be the same as what is configured as Mautic's site_url
     * in order to prevent mismatches between cached URLs generated during web requests and URLs generated
     * via CLI/cron jobs.
     */
    public function setRouterRequestContext(RequestEvent $event)
    {
        if (empty($this->host)) {
            return;
        }

        if (!$event->isMasterRequest()) {
            return;
        }

        $originalContext = $this->router->getContext();

        // Remove index_dev.php, index.php, and ending forward slash from the URL to match what is configured in SiteUrlEnvVars
        $originalBaseUrl = str_replace(['index_dev.php', 'index.php'], '', $originalContext->getBaseUrl());
        if ('/' == substr($originalBaseUrl, -1)) {
            $originalBaseUrl = substr($originalBaseUrl, 0, -1);
        }

        if ($originalBaseUrl && !$this->baseUrl) {
            // Likely in installation where the request parameters passed into this listener are not set yet so just use the original context
            return;
        }

        // Append index_dev.php for installations at the root level
        if ('dev' === MAUTIC_ENV && !str_contains($this->baseUrl, 'index_dev.php')) {
            $this->baseUrl = $this->baseUrl.'/index_dev.php';
        }

        $context = $this->router->getContext();
        $context->setBaseUrl($this->baseUrl);
        $context->setScheme($this->scheme);
        $context->setHost($this->host);
        $context->setHttpPort($this->httpPort);
        $context->setHttpsPort($this->httpsPort);
    }
}
