<?php

namespace Mautic\EmailBundle\Swiftmailer\Momentum\DTO\TransmissionDTO;

use Mautic\EmailBundle\Swiftmailer\Momentum\DTO\TransmissionDTO\RecipientDTO\AddressDTO;

/**
 * Class RecipientDTO.
 */
final class RecipientDTO implements \JsonSerializable
{
    private ?string $returnPath = null;

    private array $tags = [];

    /**
     * RecipientDTO constructor.
     *
     * @param array $metadata
     * @param array $substitutionData
     */
    public function __construct(private AddressDTO $address, private $metadata = [], private $substitutionData = [])
    {
    }

    /**
     * @param string|null $returnPath
     *
     * @return RecipientDTO
     */
    public function setReturnPath($returnPath)
    {
        $this->returnPath = $returnPath;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return RecipientDTO
     */
    public function addTag($key, $value)
    {
        $this->tags[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return RecipientDTO
     */
    public function addMetadata($key, mixed $value)
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function addSubstitutionData($key, mixed $value)
    {
        $this->substitutionData[$key] = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        $json = [
            'address' => $this->address,
        ];
        if (0 !== count($this->tags)) {
            $json['tags'] = $this->tags;
        }
        if (0 !== count($this->metadata)) {
            $json['metadata'] = $this->metadata;
        }

        if (0 === count($this->substitutionData)) {
            // `substitution_data` is required but Sparkpost will return the following error with empty arrays:
            // field 'substitution_data' is of type 'json_array', but needs to be of type 'json_object'
            $json['substitution_data'] = new \stdClass();
        } else {
            $json['substitution_data'] = $this->substitutionData;
        }

        if (null !== $this->returnPath) {
            $json['return_path'] = $this->returnPath;
        }

        return $json;
    }
}
