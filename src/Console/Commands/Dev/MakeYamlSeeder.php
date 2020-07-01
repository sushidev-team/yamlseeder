<?php

namespace AMBERSIVE\YamlSeeder\Console\Commands\Dev;

use Illuminate\Console\Command;

use Str;
use File;

use Illuminate\Console\GeneratorCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class MakeYamlSeeder extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:seeder-yaml {name} {--model=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new yaml seeder file.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $name = $this->getNameInput();
        $path        = $this->getPath($name);

        if ((! $this->hasOption('force') ||
             ! $this->option('force')) && 
            File::exists($path)) {
            $this->error('Yaml seedfile already exists!');
            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClassCustom($name, 'seed')));

        $this->info('Yaml seedfile created successfully.');

    }

    /**
     * Returns the path to the stubs folder
     */
    protected function getStub(): String {
        return __DIR__."/../../../Stubs/";
    }

    /**
     * Returns the path for the document class
     *
     * @param  mixed $name
     * @return String
     */
    protected function getPath($name):String {
        return $this->getPathFolder($name, config('yaml-seeder.path' , 'database/seeds-yaml'));
    }

    /**
     * Returns the base path for the file
     *
     * @param  mixed $name
     * @param  mixed $folder
     * @return String
     */
    protected function getPathFolder(String $name, String $folder = ''): String {
        return base_path("${folder}/${name}.yml");
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClassCustom(String $name, String $stubname) {
        $stub = $this->files->get($this->getStubFilePath($stubname));

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStubFilePath(String $stubname):String {
        return $this->getStub()."${stubname}.stub";
    }

    /**
     * Replace placeholder
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $model = str_replace("/", "\\", $this->option('model'));

        $stub = str_replace(
            ['DummyModel'],
            [$model != "" ? "\\".$model : ""],
            $stub
        );

        return $this;
    }

}
