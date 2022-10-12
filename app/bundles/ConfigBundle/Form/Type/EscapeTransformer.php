<?php

declare(strict_types=1);

namespace Mautic\ConfigBundle\Form\Type;

use Symfony\Component\Form\DataTransformerInterface;

class EscapeTransformer implements DataTransformerInterface
{
    private array $allowedParameters;

    public function __construct(array $allowedParameters)
    {
        $this->allowedParameters = array_filter($allowedParameters);
    }

    public function transform($value)
    {
        if (is_array($value)) {
            return array_map(fn($value) => $this->unescape($value), $value);
        }

        return $this->unescape($value);
    }

    public function reverseTransform($value)
    {
        if (is_array($value)) {
            return array_map(fn($value) => $this->escape($value), $value);
        }

        return $this->escape($value);
    }

    /**
     * @return mixed
     */
    private function unescape(mixed $value)
    {
        if (!is_string($value)) {
            return $value;
        }

        return str_replace('%%', '%', $value);
    }

    /**
     * @return mixed
     */
    private function escape(mixed $value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $escaped = str_replace('%', '%%', $value);

        return $this->allowParameters($escaped);
    }

    private function allowParameters(string $escaped): string
    {
        if (!$this->allowedParameters) {
            return $escaped;
        }

        $search  = array_map(fn(string $value) => "%%{$value}%%", $this->allowedParameters);
        $replace = array_map(fn(string $value) => "%{$value}%", $this->allowedParameters);

        return str_ireplace($search, $replace, $escaped);
    }
}
