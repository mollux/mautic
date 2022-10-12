<?php

namespace Mautic\CoreBundle\Helper\ListParser;

use Mautic\CoreBundle\Helper\ListParser\Exception\FormatNotSupportedException;

interface ListParserInterface
{
    /**
     * @throws FormatNotSupportedException
     */
    public function parse(mixed $list): array;
}
