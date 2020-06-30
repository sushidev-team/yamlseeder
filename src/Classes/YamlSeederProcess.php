<?php 

namespace AMBERSIVE\YamlSeeder\Classes;

use File;
use Yaml;
use DB;

class YamlSeederProcess {

    public String $path;
    public bool   $successful = false;
    public array  $yamlData = [];
    public array  $fillable = [];

    public $intance;

    public function __construct(String $path) {
        $this->path = $path;
    }
    
    /**
     * Execute the seed process
     *
     * @return bool
     */
    public function execute():bool {

        if (File::exists($this->path) === false) {
            return false;
        }

        $this->yamlData = Yaml::parseFile($this->path);
        
        $modelInstance  = $this->extractModelInstance();
        $this->fillable = $modelInstance->getFillable();
        $this->intance  = $modelInstance;

        return $this->loopLines();

    }
    
    /**
     * Extract a model Instance and check if the model exists
     *
     * @return void
     */
    private function extractModelInstance() {

        $model = data_get($this->yamlData, 'model', null);

        if ($model === null || $model === '') {
            throw ValidationException::withMessages([
                'model' => ['model does not exists or is empty. Please make sure this model exists.']
            ]);
        }

        return new $model();

    }
    
    /**
     * Go throuh all the lines
     *
     * @return void
     */
    private function loopLines():bool {

        $lines = collect(data_get($this->yamlData, 'data', []));

        $result = DB::transaction(function() use ($lines){

            $lines->each(function($line){
                $successful = $this->saveItem($line);
                if ($successful == false) {
                    throw \Exeception("Seed failed!");
                }
            });

            return true;

        });

        return $result;

    }
    
    /**
     * Save item data
     *
     * @param  mixed $item
     * @return bool
     */
    private function saveItem(array $item):bool {
        $item = $this->sanitizeItem($item);
        $primaryKey = data_get($this->yamlData, 'primary', 'id');

        $model = data_get($this->yamlData, 'model', null);

        if ($model === null) {
            return false;
        }

        $entry = $model::firstOrCreate($this->createItemData($item));
        $entry->update($item);

        return true;

    }
    
    /**
     * Create the data which is required for the model creation process
     *
     * @param  mixed $item
     * @return array
     */
    private function createItemData(array $item): array {

        $primaryKey     = data_get($this->yamlData, 'primary', 'id');
        $fieldsOnCreate = data_get($this->yamlData, 'fieldsOnCreate', []);
        $returnData     = [];

        foreach($fieldsOnCreate as $index => $value) {

            if (isset($item[$value])) {
                $returnData[$value] = $item[$value];
            }

        }

        if (isset($returnData[$primaryKey]) === false && isset($item[$primaryKey])){
            $returnData[$primaryKey] = $item[$primaryKey];
        }

        return $returnData;

    }
    
    /**
     * Remove all fields from the data array which are note allowed
     *
     * @param  mixed $item
     * @return array
     */
    private function sanitizeItem(array $item):array {
        foreach($item as $key => $value){
            if (in_array($key, $this->fillable) === false) {
                unset($item[$key]);
            }
        }
        return $item;
    }

}