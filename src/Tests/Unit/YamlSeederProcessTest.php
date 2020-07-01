<?php

namespace AMBERSIVE\YamlSeeder\Tests\Unit\Classes;


use Config;
use File;
use Tests\TestCase;

use AMBERSIVE\YamlSeeder\Classes\YamlSeederProcess;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class YamlSeederProcessTest extends TestCase
{

    use DatabaseMigrations;
    use RefreshDatabase;

    public YamlSeederProcess $process;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('yaml-seeder.path', base_path('vendor/ambersive/yamlseeder/src/Tests/Examples/Seeders'));
        File::copy(base_path('vendor/ambersive/yamlseeder/src/Tests/Examples/Seeders/demo.yml'), base_path('vendor/ambersive/yamlseeder/src/Tests/Examples/Seeders/demo.ori'));

        $this->process = new YamlSeederProcess(base_path('vendor/ambersive/yamlseeder/src/Tests/Examples/Seeders/demo.yml'));

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
    public function testIfYamlSeederProcessCanExtractModel():void {

        //$this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->process->load();
        $modelInstance = $this->invokeMethod($this->process, 'extractModelInstance');
        $this->assertNotNull($modelInstance);

    }

    public function testIfYamlSeederProcessThrowExeptionIfYamlNotLoaded():void {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $modelInstance = $this->invokeMethod($this->process, 'extractModelInstance');
    }

        
    /**
     * Make a private function callable
     *
     * @param  mixed $object
     * @param  mixed $methodName
     * @param  mixed $parameters
     * @return void
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = []) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

}