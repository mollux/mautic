<?php

namespace Mautic\CoreBundle\Loader;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\ParameterBag;

class ParameterLoader
{
    private \Symfony\Component\HttpFoundation\ParameterBag $parameterBag;

    private \Symfony\Component\HttpFoundation\ParameterBag $localParameterBag;

    private array $localParameters = [];

    private static array $defaultParameters = [];

    public function __construct(private string $rootPath = __DIR__.'/../../../')
    {
        $this->loadDefaultParameters();
        $this->loadLocalParameters();
        $this->createParameterBags();
    }

    public function getDefaultParameters(): array
    {
        return self::$defaultParameters;
    }

    public function getParameterBag(): ParameterBag
    {
        return $this->parameterBag;
    }

    public function getLocalParameterBag(): ParameterBag
    {
        return $this->localParameterBag;
    }

    public function loadIntoEnvironment()
    {
        $envVariables      = new ParameterBag();
        $defaultParameters = new ParameterBag(self::$defaultParameters);

        // Load from local configuration file first
        EnvVars\ConfigEnvVars::load($this->parameterBag, $defaultParameters, $envVariables);

        // Load special values used in Mautic configuration files in app/config
        EnvVars\ApiEnvVars::load($this->parameterBag, $defaultParameters, $envVariables);
        EnvVars\ElFinderEnvVars::load($this->parameterBag, $defaultParameters, $envVariables);
        EnvVars\MigrationsEnvVars::load($this->parameterBag, $defaultParameters, $envVariables);
        EnvVars\SAMLEnvVars::load($this->parameterBag, $defaultParameters, $envVariables);
        EnvVars\SessionEnvVars::load($this->parameterBag, $defaultParameters, $envVariables);
        EnvVars\SiteUrlEnvVars::load($this->parameterBag, $defaultParameters, $envVariables);
        EnvVars\TwigEnvVars::load($this->parameterBag, $defaultParameters, $envVariables);

        // Load the values into the environment for cache use
        $dotenv = new \Symfony\Component\Dotenv\Dotenv();
        $dotenv->populate($envVariables->all());
    }

    public static function getLocalConfigFile(string $root, $updateDefaultParameters = true): string
    {
        $paths = [];
        $root = realpath($root);

        /** @var array $paths */
        include $root.'/config/paths.php';

        if (!isset($paths['local_config'])) {
            if ($updateDefaultParameters) {
                self::$defaultParameters['local_config_path'] = $root.'/config/local.php';
            }

            return $root.'/config/local.php';
        }

        $paths['local_config'] = str_replace('%kernel.project_dir%', $root.'/..', $paths['local_config']);

        if ($updateDefaultParameters) {
            self::$defaultParameters['local_config_path'] = $paths['local_config'];
        }

        // We need this for the file manager
        if (isset($paths['local_root'])) {
            if ($updateDefaultParameters) {
                self::$defaultParameters['local_root'] = $paths['local_root'];
            }
        }

        return $paths['local_config'];
    }

    private function loadDefaultParameters(): void
    {
        if (self::$defaultParameters) {
            // This is loaded within and outside the container so use static variable to prevent recompiling
            // multiple times
            return;
        }

        $finder = (new Finder())
            ->files()
            ->followLinks()
            ->depth('== 0')
            ->in(__DIR__.'/../../../bundles/*/Config')
            ->in(__DIR__.'/../../../../plugins/*/Config')
            ->name('config.php');

        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            /** @var array $config */
            $config = include $file->getPathname();

            $parameters              = $config['parameters'] ?? [];
            self::$defaultParameters = array_merge(self::$defaultParameters, $parameters);
        }
    }

    private function loadLocalParameters(): void
    {
        $parameters = null;
        $compiledParameters = [];
        $localConfigFile    = self::getLocalConfigFile($this->rootPath);

        // Load parameters array from local configuration
        if (file_exists($localConfigFile)) {
            /** @var array $parameters */
            include $localConfigFile;

            // Override default with local
            $compiledParameters = array_merge($compiledParameters, $parameters);
        }

        // Force local specific params
        $localParametersFile = $this->getLocalParametersFile();
        if (file_exists($localParametersFile)) {
            /** @var array $parameters */
            include $localParametersFile;

            //override default with forced
            $compiledParameters = array_merge($compiledParameters, $parameters);
        }

        // Load from environment
        $envParameters = getenv('MAUTIC_CONFIG_PARAMETERS');
        if ($envParameters) {
            $compiledParameters = array_merge($compiledParameters, json_decode($envParameters, true, 512, JSON_THROW_ON_ERROR));
        }

        $this->localParameters = $compiledParameters;
    }

    private function createParameterBags(): void
    {
        $this->localParameterBag = new ParameterBag($this->localParameters);
        $this->parameterBag      = new ParameterBag(array_merge(self::$defaultParameters, $this->localParameters));
    }

    private function getLocalParametersFile(): string
    {
        return $this->rootPath.'/config/parameters_local.php';
    }
}
