<?php

use \Enrise\Soy\SymfonyBuildParameters\PrepareSymfonyEnvironmentTask;

$recipe = new \Soy\Recipe();

$recipe->prepare(\League\CLImate\CLImate::class, [PrepareSymfonyEnvironmentTask::class, 'prepareCli']);

$recipe->component('prepare-environment', function (PrepareSymfonyEnvironmentTask $environmentTask) {
    $environmentTask->run();
});

$recipe->component('prepare-local', function (PrepareSymfonyEnvironmentTask $environmentTask) {
    $environmentTask->setEnvironmentFile('files/environment/environment.local.yml')->run();
});


return $recipe;
