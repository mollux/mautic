<?php

declare(strict_types=1);

namespace MauticPlugin\MauticCrmBundle\Command;

use Mautic\CoreBundle\Templating\Helper\TranslatorHelper;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticCrmBundle\Api\PipedriveApi;
use MauticPlugin\MauticCrmBundle\Integration\Pipedrive\Import\AbstractImport;
use MauticPlugin\MauticCrmBundle\Integration\Pipedrive\Import\CompanyImport;
use MauticPlugin\MauticCrmBundle\Integration\Pipedrive\Import\LeadImport;
use MauticPlugin\MauticCrmBundle\Integration\Pipedrive\Import\OwnerImport;
use MauticPlugin\MauticCrmBundle\Integration\PipedriveIntegration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchPipedriveDataCommand extends Command
{
    public function __construct(
        private IntegrationHelper $integrationHelper,
        private TranslatorHelper $translatorHelper,
        private OwnerImport $ownerImport,
        private CompanyImport $companyImport,
        private LeadImport $leadImport
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('mautic:integration:pipedrive:fetch')
            ->setDescription('Pulls the data from Pipedrive and sends it to Mautic')
            ->addOption(
                '--restart',
                null,
                InputOption::VALUE_NONE,
                'Restart intgeration'
            );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var PipedriveIntegration $integrationObject */
        $integrationObject = $this->integrationHelper
            ->getIntegrationObject(PipedriveIntegration::INTEGRATION_NAME);

        if (!$integrationObject || !$integrationObject->getIntegrationSettings()->getIsPublished()) {
            $io->note('Pipedrive integration is disabled.');

            return 0;
        }

        $types = [
            'owner' => PipedriveApi::USERS_API_ENDPOINT,
            'lead'  => PipedriveApi::PERSONS_API_ENDPOINT,
        ];

        if ($integrationObject->isCompanySupportEnabled()) {
            $types = ['company' => PipedriveApi::ORGANIZATIONS_API_ENDPOINT] + $types;
        }

        if ($input->getOption('restart')) {
            $io->note(
                $this->translatorHelper->trans(
                    'mautic.plugin.config.integration.restarted',
                    ['%integration%' => $integrationObject->getName()]
                )
            );
            $integrationObject->removeIntegrationEntities();
        }

        foreach ($types as $type => $endPoint) {
            $this->getData($type, $endPoint, $integrationObject, $io);
        }

        $io->success('Execution time: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3));

        return 0;
    }

    private function getData(string $type, string $endPoint, PipedriveIntegration $integrationObject, SymfonyStyle $io)
    {
        $io->title('Pulling '.$type);
        $start = 0;
        $limit = 500;

        while (true) {
            $query = [
                'start' => $start,
                'limit' => $limit,
            ];
            $service = $this->getIntegrationService($type);
            $service->setIntegration($integrationObject);

            try {
                $result = $service->getData($query, $endPoint);
            } catch (\Exception) {
                return;
            }

            $io->text('Pulled '.$result['processed']);
            $io->note('Using '.memory_get_peak_usage(true) / 1_000_000 .' megabytes of ram.');

            if (!$result['more_items_in_collection']) {
                return;
            }

            $start += $limit;
            $io->text('Pulling more...');
        }
    }

    private function getIntegrationService(string $type): AbstractImport
    {
        return match ($type) {
            'owner' => $this->ownerImport,
            'lead' => $this->leadImport,
            'company' => $this->companyImport,
            default => throw new \Exception("Unknown type {$type}"),
        };
    }
}
