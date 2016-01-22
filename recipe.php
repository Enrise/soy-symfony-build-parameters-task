<?php

use Enrise\Soy\SymfonyBuildParameters\ParametersTask;
use Enrise\Soy\SymfonyBuildParameters\PrepareEnvironmentTask;
use Enrise\Soy\SymfonyBuildParameters\PrepareSymfonyEnvironmentTask;

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
    'symfony-local',
    function (PrepareSymfonyEnvironmentTask $sfEnvironmentTask, ParametersTask $parametersTask) {
        $parametersTask->setEnv('local');
        $parametersTask->setGlobalEnvFile('testglobalfile');
        $sfEnvironmentTask
            ->run();
    }
)
    ->cli([ParametersTask::class, 'prepareCli'])
    ->cli([PrepareEnvironmentTask::class, 'prepareCli'])
    ->cli([PrepareSymfonyEnvironmentTask::class, 'prepareCli'])
;

return $recipe;
