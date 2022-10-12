<?php

namespace Mautic\CoreBundle\Templating\Helper;

use Mautic\CoreBundle\Helper\AppVersion;
use Symfony\Component\Templating\Helper\Helper;

/**
 * Class VersionHelper.
 */
class VersionHelper extends Helper
{
    public function __construct(private AppVersion $appVersion)
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'version';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->appVersion->getVersion();
    }
}
