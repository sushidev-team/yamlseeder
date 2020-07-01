<?php 

namespace AMBERSIVE\YamlSeeder\Classes;

use DB;
use File;
use Str;
use Yaml;

use AMBERSIVE\YamlSeeder\Classes\YamlSeederFieldType;

use Illuminate\Validation\ValidationException;

class YamlSeederProcess {

    public String $path;
    public bool   $successful   = false;
    public bool   $saveOnFinish =  false;
    public array  $yamlData     = [];
    public array  $yamlOrginal  = [];
    public array  $fillable     = [];

    public $intance;

    public function __construct(String $path) {
        $this->path = $path;
    }
    
    /**
     * Load the yaml content into the process
     *
     * @return void
     */
    public function load() {
        $this->yamlData    = Yaml::parseFile($this->path);
        $this->yamlOrginal = Yaml::parseFile($this->path);
        return $this;
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

        if (empty($this->yamlData)){
            $this->load();
        }
        
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

            $lines->each(function($line, $index){
                $successful = $this->saveItem($line, $index);
                if ($successful == false) {
                    throw \Exeception("Seed failed!");
                }
            });

            return true;

        });

        if ($this->saveOnFinish){
            $yamlContent = Yaml::dump($this->yamlData);
            $file        = File::put($this->path, $yamlContent);
        }

        return $result;

    }
    
    /**
     * Save item data
     *
     * @param  mixed $item
     * @return bool
     */
    private function saveItem(array $item, int $index):bool {
        $itemSanitized = $this->sanitizeItem($item);
        $primaryKey = data_get($this->yamlData, 'primary', 'id');

        $model = data_get($this->yamlData, 'model', null);

        if ($model === null) {
            return false;
        }

        $entry         = $model::firstOrCreate($this->createItemData($itemSanitized));
        $itemSanitized = $this->createItemData($itemSanitized);
        $entry->update($itemSanitized);

        $this->yamlData['data'][$index] = $itemSanitized;

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

        return $this->convertData($returnData);

    }
    
    /**
     * Convert data if it is a YamlSeederFieldType
     *
     * @param  mixed $item
     * @return array
     */
    private function convertData(array $item): array {

        foreach ($item as $key => $value) {
            if (is_array($value)) {
                $type  = new YamlSeederFieldType($value);
                $field = $type->field !== "" ? $type->field : $key;
                $item[$field] = $type->transform();
                $this->saveOnFinish = true;
            }
        }

        return $item;

    }
    
    /**
     * Remove all fields from the data array which are note allowed
     *
     * @param  mixed $item
     * @return array
     */
    private function sanitizeItem(array $item):array {
        foreach($item as $key => $value){
            if (Str::endsWith($key, '_raw') === false && in_array($key, $this->fillable) === false) {
                unset($item[$key]);
            }
        }
        return $this->convertData($item);
    }

}