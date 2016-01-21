<?php

namespace Enrise\Soy\SymfonyBuildParameters;

use League\CLImate\CLImate;
use Soy\Task\TaskInterface;

class PrepareSymfonyEnvironmentTask implements TaskInterface
{
    const CLI_ARG_ENV_FILE = 'env-file';

    /**
     * @var PrepareEnvironmentTask
     */
    private $prepareEnvironmentTask;

    /**
     * @var CLImate
     */
    private $climate;

    /**
     * @var string|null
     */
    private $environmentFile;

    /**
     * @param PrepareEnvironmentTask $prepareEnvironmentTask
     * @param CLImate $climate
     */
    public function __construct(PrepareEnvironmentTask $prepareEnvironmentTask, CLImate $climate)
    {
        $prepareEnvironmentTask->setSourceFile('app/config/parameters.yml.dist');
        $prepareEnvironmentTask->setDestinationFile('app/config/parameters.yml');

        $this->prepareEnvironmentTask = $prepareEnvironmentTask;
        $this->climate = $climate;
    }

    /**
     * Finds and replaces environment specific parameters into the symfony parameters.yml file
     */
    public function run()
    {
        $envFile = $this->climate->arguments->get(self::CLI_ARG_ENV_FILE);
        if ($envFile !== null) {
            $this->environmentFile = $envFile;
        }

        if ($this->environmentFile !== null) {
            $this->prepareEnvironmentTask->setEnvFile($this->environmentFile);
        }

        $this->prepareEnvironmentTask->setEnclosingParamSymbol('%');
        $this->prepareEnvironmentTask->run();
    }

    /**
     * When linked as callback for Soy's prepare, adds a command line argument for this task.
     *
     * @param CLImate $climate
     * @return CLImate
     * @throws \Exception
     */
    public static function prepareCli(CLImate $climate)
    {
        $climate->arguments->add([
            self::CLI_ARG_ENV_FILE => [
                'longPrefix' => self::CLI_ARG_ENV_FILE,
                'description' => 'The environment file that contains the parameters',
                'required' => false
            ]
        ]);

        return $climate;
    }

    /**
     * @return null|string
     */
    public function getEnvironmentFile()
    {
        return $this->environmentFile;
    }

    /**
     * @param null|string $environmentFile
     * @return self
     */
    public function setEnvironmentFile($environmentFile)
    {
        $this->environmentFile = $environmentFile;
        return $this;
    }
}
