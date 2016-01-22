<?php

namespace Enrise\Soy\SymfonyBuildParameters;

use League\CLImate\CLImate;
use Soy\Task\TaskInterface;

class PrepareSymfonyEnvironmentTask implements TaskInterface
{
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

        $this->prepareEnvironmentTask = $prepareEnvironmentTask;
        $this->climate = $climate;
    }

    /**
     * Finds and replaces environment specific parameters into the symfony parameters.yml file
     */
    public function run()
    {
        $envFile = $this->climate->arguments->get(PrepareEnvironmentTask::CLI_ARG_ENV_FILE);
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
     * @throws \Exception
     */
    public static function prepareCli(CLImate $climate)
    {
        $args = $climate->arguments->all();
        $output = $args[PrepareEnvironmentTask::CLI_ARG_DEST_FILE];
        $output->setDefaultValue('app/config/parameters.yml');
        $output->setValue('app/config/parameters.yml');
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
