<?php

namespace Mautic\LeadBundle\Templating\Helper;

use Mautic\LeadBundle\Entity\DoNotContact;
use Mautic\LeadBundle\Exception\UnknownDncReasonException;
use Symfony\Component\Templating\Helper\Helper;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Convert DNC reason ID to text.
 */
class DncReasonHelper extends Helper
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    /**
     * Convert DNC reason ID to text.
     *
     * @param int $reasonId
     *
     * @return string
     *
     * @throws UnknownDncReasonException
     */
    public function toText($reasonId)
    {
        $reasonKey = match ($reasonId) {
            DoNotContact::IS_CONTACTABLE => 'mautic.lead.event.donotcontact_contactable',
            DoNotContact::UNSUBSCRIBED => 'mautic.lead.event.donotcontact_unsubscribed',
            DoNotContact::BOUNCED => 'mautic.lead.event.donotcontact_bounced',
            DoNotContact::MANUAL => 'mautic.lead.event.donotcontact_manual',
            default => throw new UnknownDncReasonException(sprintf("Unknown DNC reason ID '%c'", $reasonId)),
        };

        return $this->translator->trans($reasonKey);
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'lead_dnc_reason';
    }
}
