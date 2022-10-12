<?php

namespace Mautic\EmailBundle\Swiftmailer\Momentum\DTO\TransmissionDTO\ContentDTO;

/**
 * Class AttachementDTO.
 */
final class AttachementDTO implements \JsonSerializable
{
    /**
     * AttachementDTO constructor.
     *
     * @param string $type
     * @param string $name
     * @param string $content
     */
    public function __construct(private $type, private $name, private $content)
    {
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'data' => $this->content,
        ];
    }
}
