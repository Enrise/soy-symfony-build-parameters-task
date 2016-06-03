<?php

use Enrise\Soy\SymfonyBuildParameters\ParametersTask;
use Enrise\Soy\SymfonyBuildParameters\PrepareEnvironmentTask;
use Enrise\Soy\SymfonyBuildParameters\PrepareSymfonyEnvironmentTask;

// Example usage
// ParametersTask::$environmentFilenameMask = 'my.yml';
// ParametersTask::$environmentFilePath = '../some/relative/or/full/path/';
// PrepareSymfonyEnvironmentTask::$cliArgSrcFile = '../some/relative/or/full/path/app/config/parameters.yml.dist';
// PrepareSymfonyEnvironmentTask::$cliArgDestFile = '../some/relative/or/full/path/app/config/parameters.yml';

$recipe = new \Soy\Recipe();

$recipe->component('default', function (PrepareSymfonyEnvironmentTask $environmentTask) {
    $environmentTask
        ->run();
})
    ->cli([ParametersTask::class, 'prepareCli'])
    ->cli([PrepareEnvironmentTask::class, 'prepareCli'])
    ->cli([PrepareSymfonyEnvironmentTask::class, 'prepareCli'])
;

$recipe->component(
    'symfony-dev',
    function (PrepareSymfonyEnvironmentTask $sfEnvironmentTask, ParametersTask $parametersTask) {
        $parametersTask->setEnv('dev');
        $sfEnvironmentTask
            ->run();
    }
)
    ->cli([ParametersTask::class, 'prepareCli'])
    ->cli([PrepareEnvironmentTask::class, 'prepareCli'])
    ->cli([PrepareSymfonyEnvironmentTask::class, 'prepareCli'])
;

return $recipe;
