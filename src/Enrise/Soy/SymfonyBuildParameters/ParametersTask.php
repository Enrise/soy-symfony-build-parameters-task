<?php

namespace Enrise\Soy\SymfonyBuildParameters;

use League\CLImate\CLImate;
use Soy\Task\TaskInterface;
use Symfony\Component\Yaml\Yaml;

class ParametersTask implements TaskInterface
{
    const CLI_ARG_ENV = 'env';

    const CLI_ARG_ENV_PATH = 'env-path';

    const ENVIRONMENT_FILENAME_MASK = 'environment.%s.yml';

    const ENVIRONMENT_FILE_PATH_DEFAULT = 'files/environment';

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var CLImate
     */
    private $climate;

    /**
     * @param CLImate $climate
     */
    public function __construct(CLImate $climate)
    {
        $this->climate = $climate;
    }

    /**
     * Fetch the parameters form a given Yaml file and prepare them for further use
     */
    public function run()
    {

        $envFile = $this->getEnvFilename();

        if (! is_readable($envFile)) {
            $this->climate->red('Unable to read file: ' . $envFile);
            exit;
        }

        $envParams = $this->readParamsFromFile($envFile);

        $globalFile = $this->getEnvFilename('global');

        if (! is_readable($globalFile)) {
            $this->climate->yellow('No global file found, proceeding without it. File tried: ' . $globalFile);
        }

        $distEnvironmentParameters = [];
        if (is_readable($globalFile)) {
            $distEnvironmentParameters = $this->readParamsFromFile($globalFile);
        }

        if (is_array($distEnvironmentParameters)) {
            $envParams = array_merge($distEnvironmentParameters, $envParams);
        }

        $this->parameters = $envParams;
    }

    public static function prepareCli(CLImate $climate)
    {
        $climate->arguments->add([
            self::CLI_ARG_ENV_PATH => [
                'longPrefix' => self::CLI_ARG_ENV_PATH,
                'description' => 'The directory which contains the env files',
                'defaultValue' => static::ENVIRONMENT_FILE_PATH_DEFAULT,
                'required' => false,
            ],
        ]);

        $climate->arguments->add([
            self::CLI_ARG_ENV => [
                'longPrefix' => self::CLI_ARG_ENV,
                'description' => 'The current environment name. I.E.: dev, test, prod',
                'required' => false,
            ],
        ]);
    }

    /**
     * @param string $env
     * @return string
     */
    public function getEnvFilename($env = null)
    {
        $args = $this->climate->arguments;

        if ($env === null) {
            $env = $args->get(static::CLI_ARG_ENV);
        }

        $path = $args->get(static::CLI_ARG_ENV_PATH);

        return sprintf($path . '/' . static::ENVIRONMENT_FILENAME_MASK, $env);
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $filename
     * @return array
     */
    private function readParamsFromFile($filename)
    {
        $environmentContents = file_get_contents($filename);

        return Yaml::parse($environmentContents);
    }
}
