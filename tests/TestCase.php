<?php

namespace AMBERSIVE\Tests;

use Illuminate\Contracts\Console\Kernel;

use Orchestra\Testbench\TestCase as Orchestra;

use AMBERSIVE\YamlSeeder\YamlSeederServiceProvider;
use PragmaRX\Yaml\Package\ServiceProvider as YamlServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            YamlSeederServiceProvider::class,
            YamlServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Yaml' => 'PragmaRX\Yaml\Package\Facade'
        ];
    }

}
