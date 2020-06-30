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
    public static function seed(): array {

        $path = config('yaml-seeder.path', base_path('database/seeds-yaml'));

        if (File::exists($path) === false) {
            return [];
        }

        $finder    = new \Symfony\Component\Finder\Finder();
        $finder->files()->name("*.yml")->in($path);

        $results = [];

        foreach ($finder as $file) {
            $results[] = self::seedFile($file);
        }

        return $results;

    }
    
    /**
     * Execute a single yaml seed process
     *
     * @param  mixed $path
     * @return YamlSeederProcess
     */
    public static function seedFile(String $path): YamlSeederProcess {

        $seedProcess = new YamlSeederProcess($path);
        $seedProcess->execute();
        return $seedProcess;

    }

}