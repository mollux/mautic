<?php

namespace Mautic\CoreBundle\Templating\Helper;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Symfony\Component\Templating\Helper\Helper;

/**
 * Allows templates access values from CoreParametersHelper.
 *
 * Example usage:
 *
 * <?php echo $view['config']->get('default_timezone', 'UTC'); ?>
 */
class ConfigHelper extends Helper
{
    public function __construct(private CoreParametersHelper $coreParametersHelper)
    {
    }

    /**
     * @return mixed
     */
    public function get(string $name, mixed $default = null)
    {
        return $this->coreParametersHelper->get($name, $default);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'config';
    }
}
