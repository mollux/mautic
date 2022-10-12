<?php

namespace Mautic\EmailBundle\Swiftmailer\Momentum\DTO\TransmissionDTO\RecipientDTO;

/**
 * Class AddressDTO.
 */
final class AddressDTO implements \JsonSerializable
{
    private ?string $headerTo = null;

    /**
     * AddressDTO constructor.
     *
     * @param string $email
     * @param string $name
     * @param bool   $bcc
     */
    public function __construct(private $email, private $name, $bcc = false)
    {
        if (false === $bcc) {
            $this->headerTo = $email;
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $json = [
            'email' => $this->email,
            'name'  => $this->name,
        ];
        if (null !== $this->headerTo) {
            $json['header_to'] = $this->headerTo;
        }

        return $json;
    }
}
