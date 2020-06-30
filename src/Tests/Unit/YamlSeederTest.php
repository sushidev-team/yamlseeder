<?php

namespace AMBERSIVE\YamlSeeder\Tests\Unit\Classes;


use Config;
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

    }
    
    /**
     * Test if the yaml seeder will seed the yaml files into you application
     */
    public function testIfYamlSeederWillSeedEntries(){

        $result = YamlSeeder::seed();

        $count = \AMBERSIVE\YamlSeeder\Tests\Examples\Models\Migration::count();

        $this->assertEquals(2, $count);

    }

}