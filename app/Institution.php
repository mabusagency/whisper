<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    public function staff()
    {
        return $this->hasMany('App\Staff');
    }

    public function fields()
    {
        return $this->hasMany('App\Field');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}