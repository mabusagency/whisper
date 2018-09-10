<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    public function counties()
    {
        return $this->hasMany('App\Zip');
    }

    public function staff()
    {
        return $this->belongsToMany('App\Staff');
    }
}
