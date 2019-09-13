<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    //
    protected $table = 'posts';

    // Relation one to much but to the inverse ( much to one)
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function categories() {
        return $this->belongsTo('App\Categories', 'category_id');
    }
}
