<?php

namespace AMBERSIVE\YamlSeeder\Tests\Unit\Classes;


use Config;
use File;
use Tests\TestCase;

use AMBERSIVE\YamlSeeder\Classes\YamlSeeder;


use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class YamlSeederTest extends TestCase
{

    use DatabaseMigrations;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('yaml-seeder.path', base_path('vendor/ambersive/yamlseeder/src/Tests/Examples/Seeders'));

        \AMBERSIVE\YamlSeeder\Tests\Examples\Models\Migration::all()->each(function($item){
            $item->delete();
        });

        File::copy(base_path('vendor/ambersive/yamlseeder/src/Tests/Examples/Seeders/demo.yml'), base_path('vendor/ambersive/yamlseeder/src/Tests/Examples/Seeders/demo.ori'));

    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Reset the demo file
        File::delete(base_path('vendor/ambersive/yamlseeder/src/Tests/Examples/Seeders/demo.yml'));
        File::move(base_path('vendor/ambersive/yamlseeder/src/Tests/Examples/Seeders/demo.ori'), base_path('vendor/ambersive/yamlseeder/src/Tests/Examples/Seeders/demo.yml'));

    }
    
    /**
     * Test if the yaml seeder will seed the yaml files into you application
     */
    public function testIfYamlSeederWillSeedEntries(){

        $result = YamlSeeder::seed();

        $entries = \AMBERSIVE\YamlSeeder\Tests\Examples\Models\Migration::get();
        $element = $entries->first();

        $this->assertEquals(2, $entries->count());
        $this->assertEquals(99, $element->id);

    }

}