# Soy Symfony Build Parameters Task

## Basic recipe can look like

```php
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
```

This way will satisfy all tasks dependencies and also give total control.
The help command will look like:

```
Optional Arguments:
	component (default: default)
		The component to run
	--help
		Show usage
	--version
		Show version
	--no-diagnostics
		Disable diagnostics
	--recipe recipe (default: recipe.php)
		The recipe file to use
	--env-path env-path (default: files/environment)
		The directory which contains the env files
	--env env (default: dev)
		The current environment name. I.E.: dev, test, prod
	--dest-file dest-file (default: app/config/parameters.yml)
		The destination file
	--src-file src-file (default: app/config/parameters.yml.dist)
		The source file used as template for generating the dist file
```

## Parameters priorities (lower to higher)

### If you supply no options the command default ones are gonna get used:

* --env-path env-path (default: `files/environment`)
* --env env (default: `dev`)
* --dest-file dest-file (default: `app/config/parameters.yml`)
* --src-file src-file (default: `app/config/parameters.yml.dist`)

### Symfony environment variable

```shell
# SYMFONY_ENV=test ./vendor/bin/soy      
  Running Enrise\Soy\SymfonyBuildParameters\PrepareSymfonyEnvironmentTask
    Symfony Environment detected as "test"
```
	
### CLI Arguments

```shell
# SYMFONY_ENV=test ./vendor/bin/soy --env=local
  Running Enrise\Soy\SymfonyBuildParameters\PrepareSymfonyEnvironmentTask
    Symfony Environment detected as "local"
```

### Force on recipe
Recipe
```php
$recipe->component(
    'symfony-prod',
    function (PrepareSymfonyEnvironmentTask $sfEnvironmentTask, ParametersTask $parametersTask) {
        $parametersTask->setEnv('prod');
        $sfEnvironmentTask
            ->run();
    }
)
    ->cli([ParametersTask::class, 'prepareCli'])
    ->cli([PrepareEnvironmentTask::class, 'prepareCli'])
    ->cli([PrepareSymfonyEnvironmentTask::class, 'prepareCli'])
;
```

```shell
# SYMFONY_ENV=test ./vendor/bin/soy --env=local
  Running Enrise\Soy\SymfonyBuildParameters\PrepareSymfonyEnvironmentTask
    Symfony Environment detected as "prod"
```