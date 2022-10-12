<?php

namespace Mautic\EmailBundle\Swiftmailer\Momentum\DTO\TransmissionDTO\ContentDTO;

/**
 * Class FromDTO.
 */
final class FromDTO implements \JsonSerializable
{
    private ?string $name = null;

    /**
     * FromDTO constructor.
     *
     * @param string $email
     */
    public function __construct(private $email)
    {
    }

    /**
     * @param string|null $name
     *
     * @return FromDTO
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        $json = [
            'email' => $this->email,
        ];
        if (null !== $this->name) {
            $json['name'] = $this->name;
        }

        return $json;
    }
}
