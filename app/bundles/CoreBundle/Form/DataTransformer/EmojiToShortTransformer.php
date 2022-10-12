<?php

namespace Mautic\CoreBundle\Form\DataTransformer;

use Mautic\CoreBundle\Helper\EmojiHelper;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class EmojiToShortTransformer.
 */
class EmojiToShortTransformer implements DataTransformerInterface
{
    /**
     * Convert short to unicode.
     *
     * @param array|string $content
     */
    public function transform($content): string|array
    {
        if (is_array($content)) {
            foreach ($content as &$convert) {
                $convert = $this->transform($convert);
            }
        } else {
            $content = EmojiHelper::toEmoji($content, 'short');
        }

        return $content;
    }

    /**
     * Convert emoji to short bytes.
     *
     * @param array|string $content
     */
    public function reverseTransform($content): array|string
    {
        if (is_array($content)) {
            foreach ($content as &$convert) {
                $convert = $this->reverseTransform($convert);
            }
        } else {
            $content = EmojiHelper::toShort($content);
        }

        return $content;
    }
}
