<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    public function fields()
    {
        return $this->belongsTo('App\User');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function students()
    {
        return $this->belongsToMany('App\Student');
    }

    public function recruits()
    {
        return $this->belongsToMany('App\Student')->where('converted',1);
    }

    public function counties()
    {
        return $this->belongsToMany('App\County');
    }

    public function options()
    {
        return $this->belongsToMany('App\Option');
    }
}
