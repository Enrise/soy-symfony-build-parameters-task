<?php

namespace Enrise\Soy\SymfonyBuildParameters;

use Soy\Replace\ReplaceTask;
use Soy\Task\TaskInterface;

class PrepareEnvironmentTask implements TaskInterface
{
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
     * @param ParametersTask $parametersTask
     * @param ReplaceTask $replaceTask
     */
    public function __construct(ParametersTask $parametersTask, ReplaceTask $replaceTask)
    {
        $this->parametersTask = $parametersTask;
        $this->replaceTask = $replaceTask;
    }

    /**
     * Replace the parameters set in envfile with placeholders in the source file
     * and write them to destination file
     */
    public function run()
    {
        if ($this->sourceFile === null || $this->destinationFile === null) {
            $exceptionMessage = sprintf('Please provide a source- and destination file for %s', self::class);
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
