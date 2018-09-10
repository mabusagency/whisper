<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FieldStudent extends Model
{
    protected $table = 'field_student';

    public function scopeOnlyConverted($query) {

        if(\Auth::user()->type != 'admin') {
            $query->join('students','students.id','=','field_student.student_id')
                ->where('students.converted','=',1);
        }

        return $query;
    }

}
