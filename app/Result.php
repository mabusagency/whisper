<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{

    public function scopeOnlyConverted($query) {

        if(\Auth::user()->type != 'admin') {
            $query->whereRaw('results.student_id IN (select id from students where campaign_id = results.campaign_id and converted = 1)');
        }

        return $query;
    }

}