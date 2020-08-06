<?php

namespace AMBERSIVE\YamlSeeder\Tests\Unit\Classes;


use Config;
use File;
use AMBERSIVE\Tests\TestCase;

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

        Config::set('yaml-seeder.path', __DIR__.'/../Examples/Seeders');

        \AMBERSIVE\Tests\Examples\Models\Migration::all()->each(function($item){
            $item->delete();
        });

        File::copy(__DIR__.'/../Examples/Seeders/demo.yml', __DIR__.'/../Examples/Seeders/demo.ori');

    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Reset the demo file
        File::delete(__DIR__.'/../Examples/Seeders/demo.yml');
        File::move(__DIR__.'/../Examples/Seeders/demo.ori', __DIR__.'/../Examples/Seeders/demo.yml');

    }
    
    /**
     * Test if the yaml seeder will seed the yaml files into you application
     */
    public function testIfYamlSeederWillSeedEntries(){

        $result = YamlSeeder::seed();

        $entries = \AMBERSIVE\Tests\Examples\Models\Migration::get();
        $element = $entries->first();

        $this->assertEquals(2, $entries->count());
        $this->assertEquals(99, $element->id);

    }

}