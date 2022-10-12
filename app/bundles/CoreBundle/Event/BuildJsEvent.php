<?php

namespace Mautic\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class BuildJsEvent.
 */
class BuildJsEvent extends Event
{
    /**
     * @param bool $debugMode
     * @param string $js
     */
    public function __construct(protected $js, protected $debugMode = false)
    {
    }

    /**
     * @return string
     */
    public function getJs()
    {
        return $this->debugMode ? $this->js : \JSMin::minify($this->js);
    }

    /**
     * Append JS.
     *
     * @param string $js
     * @param string $section The section name. Shows when in debug mode
     *
     * @return $this
     */
    public function appendJs($js, $section = '')
    {
        $slashes = null;
        if ($section && $this->debugMode) {
            $slashes = str_repeat('/', strlen($section) + 10);
            $this->js .= <<<JS
\n
{$slashes}
// {$section} Start
{$slashes}
\n
JS;
        }

        $this->js .= $js;

        if ($section && $this->debugMode) {
            $this->js .= <<<JS
\n
{$slashes}
// {$section} End
{$slashes}
JS;
        }

        return $this;
    }
}
