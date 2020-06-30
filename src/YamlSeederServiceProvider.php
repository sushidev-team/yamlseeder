<?php

namespace AMBERSIVE\YamlSeeder;

use App;
use Str;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;

use Illuminate\Support\ServiceProvider;

use AMBERSIVE\YamlSeeder\Classes\YamlSeeder;

class YamlSeederServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
       
        // Configs
        $this->publishes([
            __DIR__.'/Configs/yaml-seeder.php'         => config_path('yaml-seeder.php'),
        ],'yaml-seeder');

        $this->mergeConfigFrom(
            __DIR__.'/Configs/yaml-seeder.php', 'yaml-seeder.php'
        );

        // Commands

        if ($this->app->runningInConsole()) {
            if ($this->isConsoleCommandContains([ 'db:seed', '--seed' ], [ '--class', 'help', '-h' ])) {
                $this->addSeedsAfterConsoleCommandFinished();
            }
        }

    }
    
    /**
     * This command will execute the custom seeding process
     *
     * @return void
     */
    private function addSeedsAfterConsoleCommandFinished():void {

        Event::listen(CommandFinished::class, function(CommandFinished $event) {
            YamlSeeder::seed();
        });

    }

    /**
     * Get a value that indicates whether the current command in console
     * contains a string in the specified $fields.
     *
     * @param string|array $contain_options
     * @param string|array $exclude_options
     *
     * @return bool
     */
    protected function isConsoleCommandContains($contain_options, $exclude_options = null) : bool
    {
        $args = Request::server('argv', null);
        if (is_array($args)) {
            $command = implode(' ', $args);
            if (Str::contains($command, $contain_options) && ($exclude_options == null || !Str::contains($command, $exclude_options))) {
                return true;
            }
        }
        return false;
    }

}
