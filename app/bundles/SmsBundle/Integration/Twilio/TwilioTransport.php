<?php

namespace Mautic\SmsBundle\Integration\Twilio;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\SmsBundle\Sms\TransportInterface;
use Psr\Log\LoggerInterface;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioTransport implements TransportInterface
{
    private ?\Twilio\Rest\Client $client = null;

    private ?string $sendingPhoneNumber = null;

    /**
     * TwilioTransport constructor.
     */
    public function __construct(private Configuration $configuration, private LoggerInterface $logger)
    {
    }

    /**
     * @param string $content
     */
    public function sendSms(Lead $lead, $content): bool|string
    {
        $number = $lead->getLeadPhoneNumber();

        if (null === $number) {
            return false;
        }

        try {
            $this->configureClient();

            $this->client->messages->create(
                $this->sanitizeNumber($number),
                [
                    'from' => $this->sendingPhoneNumber,
                    'body' => $content,
                ]
            );

            return true;
        } catch (NumberParseException|TwilioException $exception) {
            $this->logger->addWarning(
                $exception->getMessage(),
                ['exception' => $exception]
            );

            return $exception->getMessage();
        } catch (ConfigurationException $exception) {
            $message = $exception->getMessage() ?: 'mautic.sms.transport.twilio.not_configured';
            $this->logger->addWarning(
                $message,
                ['exception' => $exception]
            );

            return $message;
        }
    }

    /**
     * @param string $number
     *
     * @return string
     *
     * @throws NumberParseException
     */
    private function sanitizeNumber($number)
    {
        $util   = PhoneNumberUtil::getInstance();
        $parsed = $util->parse($number, 'US');

        return $util->format($parsed, PhoneNumberFormat::E164);
    }

    /**
     * @throws ConfigurationException
     */
    private function configureClient()
    {
        if ($this->client) {
            // Already configured
            return;
        }

        $this->sendingPhoneNumber = $this->configuration->getSendingNumber();
        $this->client             = new Client(
            $this->configuration->getAccountSid(),
            $this->configuration->getAuthToken()
        );
    }
}
