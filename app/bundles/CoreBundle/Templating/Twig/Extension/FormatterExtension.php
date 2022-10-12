<?php

declare(strict_types=1);

namespace Mautic\CoreBundle\Templating\Twig\Extension;

use Mautic\CoreBundle\Templating\Helper\FormatterHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormatterExtension extends AbstractExtension
{
    public function __construct(protected FormatterHelper $formatterHelper)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('format', [$this, '_'], ['is_safe' => ['all']]),
        ];
    }

    /**
     * Format a string.
     */
    public function _(mixed $val, string $type = 'html', bool $textOnly = false, int $round = 1): string
    {
        return $this->formatterHelper->_($val, $type, $textOnly, $round);
    }
}
