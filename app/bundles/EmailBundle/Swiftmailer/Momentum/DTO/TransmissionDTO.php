<?php

namespace Mautic\EmailBundle\Swiftmailer\Momentum\DTO;

use Mautic\EmailBundle\Swiftmailer\Momentum\DTO\TransmissionDTO\ContentDTO;
use Mautic\EmailBundle\Swiftmailer\Momentum\DTO\TransmissionDTO\OptionsDTO;
use Mautic\EmailBundle\Swiftmailer\Momentum\DTO\TransmissionDTO\RecipientDTO;

/**
 * Class Mail.
 */
class TransmissionDTO implements \JsonSerializable
{
    /**
     * @var RecipientDTO[]
     */
    private array $recipients = [];

    /**
     * @var string|null
     */
    private $campaignId;

    private ?string $description = null;

    /**
     * TransmissionDTO constructor.
     *
     * @param string $returnPath
     */
    public function __construct(private ContentDTO $content, private $returnPath, private ?\Mautic\EmailBundle\Swiftmailer\Momentum\DTO\TransmissionDTO\OptionsDTO $options = null)
    {
    }

    /**
     * @return TransmissionDTO
     */
    public function addRecipient(RecipientDTO $recipientDTO)
    {
        $this->recipients[] = $recipientDTO;

        return $this;
    }

    /**
     * @param $campaignId
     */
    public function setCampaignId($campaignId)
    {
        $this->campaignId = $campaignId;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        $json = [
            'return_path' => $this->returnPath,
            'recipients'  => $this->recipients,
            'content'     => $this->content,
        ];
        if (null !== $this->options) {
            $json['options'] = $this->options;
        }
        if (!empty($this->campaignId)) {
            $json['campaign_id'] = $this->campaignId;
        }
        if (!empty($this->description)) {
            $json['description'] = $this->description;
        }

        return $json;
    }
}
