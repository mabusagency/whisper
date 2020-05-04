<?php

namespace App\Helpers;

use App\Campaign;
use App\County;
use App\CountyStaff;
use App\Field;
use App\FieldStudent;
use App\Option;
use App\Staff;
use App\StaffOptions;
use App\StaffStudent;
use App\Student;
use App\Zipcodes;

class StudentHelper
{

    public $standard_field_names = ['firstName', 'lastName', 'email', 'phone', 'address', 'address2', 'city', 'state', 'zip'];

    public static function get_purl_url($student) {

        if(!$campaign = session('campaign')) {
            $campaign = Campaign::find($student->campaign_id);
        }

        $domain = $campaign->domain;

        return $domain.'/'.ucfirst($student->purl1).'.'.ucfirst($student->purl2);

    }

    public static function get_testing_purl_url($student) {

        $campaign = Campaign::find($student->campaign_id);

        $domain = $campaign->domain;

        return 'http://'.$domain.'/'.$campaign->directory.'?purl='.$student->purl1.$student->purl2.'&campaign='.$campaign->id.'&test=Y';

    }

    public static function match_to_staff($student) {

        //$student->staff()->detach();

        $sf = self::get_student_custom_fields($student);

        $county = self::get_student_county($student);

        //Match to county
        if($county) {
            $cs = CountyStaff::where('institution_id',$student->institution_id)->where('county_id',$county->id)->first();
            if($cs) {
                $staff = Staff::find($cs->staff_id);
                $staff->students()->attach($student->id);
            }
        }

        //Match to field
        $fields = Field::where('institution_id',$student->institution_id)
            ->has('options')
            ->get();
        foreach($fields as $field) {
            if($option = Option::where('field_id',$field->id)->where('name',$sf[$field->id])->first()) {
                $so = StaffOptions::where('institution_id',$student->institution_id)->where('option_id',$option->id)->first();
                if($so) {
                    $staff = Staff::find($so->staff_id);

                    //check to see if another staff with the same "role" has already been assigned
                    $role = $staff->role;
                    $role_exists = false;
                    $sss = StaffStudent::where('student_id',$student->id)->get();
                    foreach($sss as $ss) {
                        $ss_staff = Staff::find($ss->staff_id);
                        if($ss_staff->role == $role) {
                            $role_exists = true;
                        }
                    }

                    if(!$role_exists) {
                        $staff->students()->attach($student->id);
                    }

                }
            }
        }

        return $student;

    }


    public static function get_student_custom_fields(Student $student) {

        $data = [];
        foreach (Field::where('institution_id',$student->institution_id)->get() as $field) {

            $value = '';

            $fs = FieldStudent::where('field_id',$field->id)
                ->where('student_id',$student->id)
                ->first();

            if($fs) $value = $fs->value;

            $data = $data + [$field->id => trim($value)];
        }

        return $data;
    }

    public function make_purl_unique(Student $student) {

        $existing_student = Student::where('id','<>',$student->id)
            ->where('institution_id',session('institution')->id)
            ->where('purl1', $student->purl1)
            ->where('purl2', $student->purl2)
            ->first();

        if($existing_student) {

            $student->purl2 = $student->purl2.rand(1,999);
            $student->save();

        }

        return $student;

    }

    public function format_field_for_mailchimp_tag($string) {

        if($string == 'firstName') $string = 'FNAME';
        if($string == 'lastName') $string = 'LNAME';

        $string = preg_replace("/[^A-Za-z0-9]/", '', $string);;
        $string = strtoupper(substr($string,0,10));

        return $string;
    }

    public static function get_student_county(Student $student) {
        $zip = Zipcodes::where('code',$student->zip)->first();
        if($zip && $zip->county) {
            return $zip->county;
        }
        return false;
    }



}