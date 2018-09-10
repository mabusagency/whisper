<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    public function students()
    {
        return $this->hasMany('App\FieldStudent');
    }

    public function options()
    {
        return $this->hasMany('App\Option');
    }
}
