<?php

namespace AMBERSIVE\YamlSeeder\Tests\Examples\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Migration extends Model
{

    public $timestamps = false;

    /**
    * The database table used by the model.
    * @var string
    */
    protected $table = 'migrations';

    /**
    * The attributes that are mass assignable.
    * @var array
    */
    protected $fillable = [
        "id",
        "migration",
        "batch"
    ];

    /**
    * The attributes excluded from the model's JSON form.
    * @var array
    */
    protected $hidden = [
        
    ];

    /**
    * This attributes get added to the list / model
    * @var array
    */
    protected $appends = [
    
    ];

    /**
    * The attributes casted and transformed to real values
    * @var array
    */
    protected $casts = [
        "batch" => 'integer'
    ];

}
