<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['institution_id', 'campaign_id', 'staff_id',
        'purl1','purl2',
        'firstName', 'lastName', 'email', 'phone', 'address', 'address2', 'city', 'state', 'zip',
        'created_at', 'updated_at'];

    public function fields()
    {
        return $this->hasMany('App\FieldStudent');
    }

    public function staff()
    {
        return $this->belongsToMany('App\Staff');
    }

    public function results()
    {
        return $this->hasMany('App\Result');
    }

    public function notes()
    {
        return $this->hasMany('App\Note');
    }

    public function scopeFilter($query) {

        if($pages = \Request::input('page')) {
            foreach($pages as $page) {
                if(substr($page,0,1) == '-') {
                    $page = str_replace('-','',$page);
                    $query->has('results')
                        ->whereRaw('students.id not in (select student_id from results where page = "'.$page.'")');
                } else {
                    $query->whereHas('results', function ($query) use($page) {
                        $query->where('page', $page);
                    });
                }
            }
        }

        if(\Request::input('notes') == 'Y') {
            $query->has('notes');
        } elseif(\Request::input('notes') == 'N') {
            $query->doesntHave('notes');
        }

        if(\Request::input('contacted') == 'Y') {
            $query->where('status',1);
        } elseif(\Request::input('contacted') == 'N') {
            $query->where('status','');
        }

        if(\Request::input('deleted') == 'Y') {
            $query->withTrashed();
        }

        if(\Auth::user()->type != 'admin') {
            $query->where('converted',1);
        }

        if(\Auth::user()->type == 'staff') {
            $query->whereRaw('id in (select student_id from staff_student where staff_id = '.\Auth::user()->staff->id.')');
        }

        return $query;
    }


}
