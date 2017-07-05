<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use SoftDeletes;
    protected $fillable = ['table_desc'];

    public function sales(){
        return $this->hasMany('App\Sale');
    }
}
