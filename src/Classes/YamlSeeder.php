<?php 

namespace AMBERSIVE\YamlSeeder\Classes;

use File;

use AMBERSIVE\YamlSeeder\Classes\YamlSeederProcess;

class YamlSeeder {
    
    /**
     * Execute all yaml files from the seeder path
     *
     * @return array
     */
    public static function seed(bool $runAsPreCommand = false): array {

        $path = config('yaml-seeder.path', base_path('database/seeds-yaml'));

        if (File::exists($path) === false) {
            return [];
        }

        if ($runAsPreCommand){
            dd('asd');
        }

        $finder    = new \Symfony\Component\Finder\Finder();
        $finder->files()->name("*.yml")->in($path);

        $results = [];

        foreach ($finder as $file) {
            $results[] = self::seedFile($file, $runAsPreCommand);
        }

        return $results;

    }
    
    /**
     * Execute a single yaml seed process
     *
     * @param  mixed $path
     * @return YamlSeederProcess
     */
    public static function seedFile(String $path, bool $runAsPreCommand = false): YamlSeederProcess {

        $seedProcess = new YamlSeederProcess($path);

        if ($runAsPreCommand && $seedProcess->load()->runAsPre() === true && $seedProcess->load()->exclude() === false) {
            $seedProcess->execute();
        }
        else if ($seedProcess->load()->runAsPre() === false && $seedProcess->load()->exclude() === false) {
            $seedProcess->execute();
        }
        return $seedProcess;

    }

}