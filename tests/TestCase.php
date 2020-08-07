<?php

namespace AMBERSIVE\Tests;

use Illuminate\Contracts\Console\Kernel;

use Orchestra\Testbench\TestCase as Orchestra;

use AMBERSIVE\YamlSeeder\YamlSeederServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            YamlSeederServiceProvider::class
        ];
    }

}
