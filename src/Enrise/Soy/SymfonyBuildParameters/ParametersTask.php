<?php

namespace Enrise\Soy\SymfonyBuildParameters;

use Soy\Task\TaskInterface;
use Symfony\Component\Yaml\Yaml;

class ParametersTask implements TaskInterface
{
    const DEFAULT_ENVIRONMENT_FILENAME = 'files/environment/environment.yml';
    const DEFAULT_DIST_ENVIRONMENT_FILENAME = 'files/environment/environment.dist.yml';

    /**
     * @var string
     */
    private $environmentFilename = self::DEFAULT_ENVIRONMENT_FILENAME;

    /**
     * @var string
     */
    private $distEnvironmentFilename = self::DEFAULT_DIST_ENVIRONMENT_FILENAME;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * Fetch the parameters form a given Yaml file and prepare them for further use
     */
    public function run()
    {
        if (! is_readable($this->environmentFilename)) {
            $errorMessage = sprintf(
                'File "%s" must be readable when using the parameters task.',
                $this->environmentFilename
            );
            throw new \RuntimeException($errorMessage);
        }

        $envParams = $this->readParamsFromFile($this->environmentFilename);
        $distEnvironmentParameters = [];
        if (is_readable($this->distEnvironmentFilename)) {
            $distEnvironmentParameters = $this->readParamsFromFile($this->distEnvironmentFilename);
        }

        if (is_array($distEnvironmentParameters)) {
            $envParams = array_merge($distEnvironmentParameters, $envParams);
        }

        $this->parameters = $envParams;
    }

    /**
     * @param string $environmentFilename
     * @return self
     */
    public function setEnvironmentFilename($environmentFilename)
    {
        $this->environmentFilename = $environmentFilename;
        return $this;
    }

    /**
     * @param string $distEnvironmentFilename
     * @return self
     */
    public function setDistEnvironmentFilename($distEnvironmentFilename)
    {
        $this->distEnvironmentFilename = $distEnvironmentFilename;
        return $this;
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
        $environmentParameters = Yaml::parse($environmentContents);
        return $environmentParameters;
    }
}
