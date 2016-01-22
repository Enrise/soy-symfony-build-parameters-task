<?php

namespace Enrise\Soy\SymfonyBuildParameters;

use League\CLImate\CLImate;
use Soy\Task\TaskInterface;

class PrepareSymfonyEnvironmentTask implements TaskInterface
{
    const CLI_ARG_ENV_DEFAULT = 'dev';

    const CLI_ARG_DEST_FILE_DEFAULT = 'app/config/parameters.yml';

    const CLI_ARG_SRC_FILE_DEFAULT = 'app/config/parameters.yml.dist';

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
        $this->prepareEnvironmentTask = $prepareEnvironmentTask;
        $this->climate = $climate;
    }

    /**
     * Finds and replaces environment specific parameters into the symfony parameters.yml file
     */
    public function run()
    {

        $this->prepareEnvironmentTask->setEnclosingParamSymbol('%');
        $this->prepareEnvironmentTask->run();
    }

    /**
     * Since SymfonyTask inherits from EnvironmentTask we are setting
     * the default output file as symfony standard parameters.yml
     * as well for the source file which is parameters.yml.dist
     *
     * @param CLImate $climate
     * @throws \Exception
     */
    public static function prepareCli(CLImate $climate)
    {
        $args = $climate->arguments->all();

        $destFile = $args[PrepareEnvironmentTask::CLI_ARG_DEST_FILE];
        $destFile->setDefaultValue(static::CLI_ARG_DEST_FILE_DEFAULT);
        $destFile->setValue(static::CLI_ARG_DEST_FILE_DEFAULT);

        $srcFile = $args[PrepareEnvironmentTask::CLI_ARG_SRC_FILE];
        $srcFile->setDefaultValue(static::CLI_ARG_SRC_FILE_DEFAULT);
        $srcFile->setValue(static::CLI_ARG_SRC_FILE_DEFAULT);

        $env = $args[ParametersTask::CLI_ARG_ENV];
        $env->setDefaultValue(static::CLI_ARG_ENV_DEFAULT);
        $env->setValue(static::CLI_ARG_ENV_DEFAULT);
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
