<?php 

namespace AMBERSIVE\YamlSeeder\Classes;

use File;
use Symfony\Component\Yaml\Yaml;
use DB;

class YamlSeederFieldType {

    public String $field = "";
    public String $convertTo = "";
    public $value = null;

    public function __construct(array $attributes) {
        foreach ($attributes as $key => $attrbute){
            if (isset($this->$key)) {
                $this->$key = $attrbute;
            }
        }
        $this->value = $attributes['value'];
    }
    
    /**
     * Transform yaml data
     *
     * @return void
     */
    public function transform() {

        switch($this->convertTo){
            case 'password':
                $this->value = bcrypt($this->value);
                break;
        }

        return $this->value;
    }

}