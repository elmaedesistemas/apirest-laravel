<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    //
    protected $table = 'categories';

    //Relacion de uno a muchos
    public function categories(){
        return($this->hasMany('App\Postss'));
    }
}
