<?php

namespace Mautic\EmailBundle\Swiftmailer\Momentum\Adapter;

use Mautic\EmailBundle\Swiftmailer\Momentum\DTO\TransmissionDTO;
use SparkPost\SparkPost;
use SparkPost\SparkPostPromise;

/**
 * Class Adapter.
 */
final class Adapter implements AdapterInterface
{
    /**
     * Adapter constructor.
     */
    public function __construct(private SparkPost $momentumSparkpost)
    {
    }

    /**
     * @return SparkPostPromise
     */
    public function createTransmission(TransmissionDTO $transmissionDTO)
    {
        $payload = json_decode(json_encode($transmissionDTO, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);

        return $this->momentumSparkpost->transmissions->post($payload);
    }
}
