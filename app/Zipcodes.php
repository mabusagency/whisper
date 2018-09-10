<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zipcodes extends Model
{
    public function county()
    {
        return $this->belongsTo('App\County');
    }
}
