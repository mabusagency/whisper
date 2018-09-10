<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    public function field()
    {
        return $this->belongsTo('App\Field');
    }

    public function staff()
    {
        return $this->belongsToMany('App\Staff');
    }
}
