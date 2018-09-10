<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = ['institution_id', 'mailchimp_list_id', 'name', 'domain', 'ftp_username', 'ftp_path', 'created_at', 'updated_at'];

    public function institution()
    {
        return $this->belongsTo('App\Institution');
    }

    public function students()
    {
        return $this->hasMany('App\Student');
    }

    public function recruits()
    {
        return $this->hasMany('App\Student')->where('converted',1);
    }
}
