<?php

namespace Enrise\Soy\SymfonyBuildParameters;

use League\CLImate\CLImate;
use Soy\Replace\ReplaceTask;
use Soy\Task\TaskInterface;

class PrepareEnvironmentTask implements TaskInterface
{
    const CLI_ARG_ENV_FILE = 'env-file';
    const CLI_ARG_DEST_FILE = 'dest-file';

    /**
     * @var ParametersTask
     */
    private $parametersTask;

    /**
     * @var ReplaceTask
     */
    private $replaceTask;

    /**
     * @var string
     */
    private $sourceFile;

    /**
     * @var string
     */
    private $destinationFile;

    /**
     * @var string
     */
    private $enclosingSymbol = '';

    /**
     * @var string
     */
    private $envFile;

    /**
     * @var CLImate
     */
    private $climate;

    /**
     * @param ParametersTask $parametersTask
     * @param ReplaceTask $replaceTask
     * @param CLImate $CLImate
     */
    public function __construct(ParametersTask $parametersTask, ReplaceTask $replaceTask, CLImate $CLImate)
    {
        $this->parametersTask = $parametersTask;
        $this->replaceTask = $replaceTask;
        $this->climate = $CLImate;
    }

    /**
     * Replace the parameters set in envfile with placeholders in the source file
     * and write them to destination file
     */
    public function run()
    {
        if (!$this->destinationFile) {
            $this->destinationFile = $this->climate->arguments->get(self::CLI_ARG_DEST_FILE);
        }

        if ($this->sourceFile === null || $this->destinationFile === null) {
            $exceptionMessage = sprintf('Please provide a source and destination file for %s', self::class);
            throw new \RuntimeException($exceptionMessage);
        }

        if ($this->envFile !== null) {
            $this->parametersTask->setEnvironmentFilename($this->envFile);
        }

        $this->parametersTask->run();

        $replacements = [];
        foreach ($this->parametersTask->getParameters() as $key => $value) {
            $key = $this->enclosingSymbol . $key . $this->enclosingSymbol;

            $replacements[$key] = $value;
        }

        $this->replaceTask
            ->setReplacements($replacements)
            ->setSource($this->sourceFile)
            ->setDestination($this->destinationFile)
        ;

        $this->replaceTask->run();
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

        $climate->arguments->add([
            self::CLI_ARG_DEST_FILE => [
                'longPrefix' => self::CLI_ARG_DEST_FILE,
                'description' => 'The destination file',
                'required' => false
            ]
        ]);
    }

    /**
     * @return string
     */
    public function getSourceFile()
    {
        return $this->sourceFile;
    }

    /**
     * @param string $sourceFile
     * @return self
     */
    public function setSourceFile($sourceFile)
    {
        $this->sourceFile = $sourceFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationFile()
    {
        return $this->destinationFile;
    }

    /**
     * @param string $destinationFile
     * @return self
     */
    public function setDestinationFile($destinationFile)
    {
        $this->destinationFile = $destinationFile;
        return $this;
    }

    /**
     * @param string $enclosingSymbol
     * @return self
     */
    public function setEnclosingParamSymbol($enclosingSymbol)
    {
        $this->enclosingSymbol = $enclosingSymbol;
        return $this;
    }

    /**
     * @param string $envFile
     * @return self
     */
    public function setEnvFile($envFile)
    {
        $this->envFile = $envFile;
        return $this;
    }
}
