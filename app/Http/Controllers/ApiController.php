<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Field;
use App\FieldStudent;
use App\Helpers\FieldHelper;
use App\Helpers\MailChimpHelper;
use App\Helpers\StudentHelper;
use App\Option;
use App\Result;
use App\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ApiController extends Controller
{

    public function visitor(Request $request)
    {

        $sh = new StudentHelper();

        if (!$request['purl']) {
            echo json_encode(['error' => 'no purl provided']);
            exit;
        }
        if (!$request['campaign']) {
            echo json_encode(['error' => 'no campaign provided']);
            exit;
        }

        if (!$campaign = Campaign::find($request['campaign'])) {
            echo json_encode(['error' => 'no campaign found']);
            exit;
        }

        $student = Student::where(\DB::raw("CONCAT(purl1,purl2)"), $request['purl'])->where('campaign_id',$campaign->id)->first();

        $response = [
            'id' => $student->id,
            'purl' => $student->purl1.$student->purl2,
            'campaign_id' => $campaign->id,
            'redirect_page' => $campaign->redirect_page,
            'converted' => $student->converted,
            'FNAME' => $student->firstName,
            'LNAME' => $student->lastName,
            'EMAIL' => $student->email,
            'PHONE' => $student->phone,
            'ADDRESS' => $student->address,
            'ADDRESS2' => $student->address2,
            'CITY' => $student->city,
            'STATE' => $student->state,
            'ZIP' => $student->zip,
        ];

        //Custom Fields
        $student_fields = $sh->get_student_custom_fields($student);
        foreach (Field::where('institution_id', $student->institution_id)->get() as $field) {
            $response[$field->tag] = $student_fields[$field->id];
        }

        //Get Page Name
        $page = explode('/',$request['path']);
        $page = $page[count($page)-1];
        $page = explode('.',$page);
        $page = $page[0];
        if($page == '' || $page == 'index') $page = 'home';

        //Check to see if page has been visited within the last day
//        if($previousVisit = Result::where('student_id',$student->id)->where('page',$page)->orderBy('id','desc')->first()) {
//            $now = Carbon::now();
//            if($previousVisit->created_at->diffInDays($now) == 0) {
//                echo json_encode($response);
//                exit;
//            }
//        }
//
//        $response['testing'] = 'N';
//        if ($request['test'] == 'Y') {
//            $response['testing'] = 'Y';
//            echo json_encode($response);
//            exit;
//        }

        //If last page is visited
        if($campaign->last_page && strstr($campaign->last_page,$page)) {
            //Match to Staff
            StudentHelper::match_to_staff($student);

            //Mark as converted
            $student->converted = 1;
            $student->save();

            //Add to MailChimp
            $mh = new MailChimpHelper($campaign);
            $mh->add_student($student); //Add Contact to MailChimp List
            $response['mailchimp_response'] = $mh->add_student_to_campaign($student); //Add to MailChimp campaign
        }

        //Save Results
        $result = new Result();
        $result->campaign_id = $campaign->id;
        $result->student_id = $student->id;
        $result->ip = $this->get_visitor_ip($request);
        $result->url = $request['url'];
        $result->page = $page;
        $result->save();

        //Return assigned staff
        foreach(config('app.roles') as $role) {
            if(isset($student->staff->where('role',$role)->first()->name)) {
                $merge_field = $sh->format_field_for_mailchimp_tag($role);
                $response[$merge_field] = $student->staff->where('role',$role)->first()->name;
            }
        }

        //Touch student (for table sorting)
        $student->touch();

        echo json_encode($response);
        exit;
    }

    public function submit(Request $request)
    {

        $domain = $this->format_domain($_SERVER['HTTP_ORIGIN']);
        $student = Student::where('id', $request->student_id)->first();
        $campaign = Campaign::where('domain', $domain)->where('id', $student->campaign_id)->find($request->purl_campaign);
        //$campaign = Campaign::where('id', $student->campaign_id)->find($request->purl_campaign);

        $original_email = $student->email; //for MailChimp

        $tags = [];
        $fields = Field::select('tag')->where('institution_id', $student->institution_id)->get();
        foreach ($fields as $field) {
            array_push($tags, strtoupper($field->tag));
        }

        foreach (Input::all() as $tag => $value) {

            if (!$value) continue;

            $tag = strtolower($tag);

            if ($tag == 'purl_campaign'
                || $tag == 'student_id'
            ) continue;

            if ($tag == 'fname') $tag = 'firstName';
            if ($tag == 'lname') $tag = 'lastName';

            //Save to primary fields
            if (array_key_exists($tag,$student->getAttributes())) {
                $student->$tag = $value;
            } //Save to custom fields
            elseif (in_array(strtoupper($tag), $tags)) {
                $field = Field::where('institution_id', $student->institution_id)->where('tag', strtoupper($tag))->first();

                if (!$fs = FieldStudent::where('field_id', $field->id)->where('student_id', $student->id)->first()) {
                    $fs = new FieldStudent();
                    $fs->student_id = $student->id;
                    $fs->field_id = $field->id;
                }
                $fs->value = $value;
                $fs->save();
            } //Create new custom fields
            else {

                $fh = new FieldHelper();
                $field = $fh->create_field($tag, $campaign);

                if (!strstr($value, '|*')) {
                    $fs = new FieldStudent();
                    $fs->student_id = $student->id;
                    $fs->field_id = $field->id;
                    $fs->value = $value;
                    $fs->save();
                }

            }

            $student->save();

            //Update MailChimp
            $mh = new MailChimpHelper($campaign);
            $mh->update_student($student, $original_email); //Add Contact to MailChimp List

        }

        echo 'saved';

    }

    public function link(Request $request) {

        if(!$request['link'] || !$request['student_id']) {
            echo 'missing visitor data';
            exit;
        }

        if(!$student = Student::find($request['student_id'])) {
            echo 'visitor not found';
            exit;
        }

        //Check to see if page has been visited within the last day
        if($previousVisit = Result::where('student_id',$request['student_id'])->where('url',$request['link'])->orderBy('id','desc')->first()) {
            $now = Carbon::now();
            if($previousVisit->created_at->diffInDays($now) == 0) {
                echo 'double click';
                exit;
            }
        }

        //Add new result
        $result = new Result();
        $result->campaign_id = $student->campaign_id;
        $result->student_id = $student->id;
        $result->ip = $this->get_visitor_ip($request);
        $result->url = $request['link'];
        $result->page = 'link';
        $result->save();

        echo 'saved';


    }

    public function options(Request $request) {

        $input_name = $request['input_name'];
        $options = $request['options'];
        $campaign_id = $request['campaign_id'];

        if(!is_array($options)) {
            echo 'no options found';
            exit;
        }

        if(!$campaign = Campaign::find($campaign_id)) {
            echo 'no campaign found';
            exit;
        }
        
        $student = Student::where('campaign_id', $campaign_id)->first();
        if ($student && array_key_exists(strtolower($input_name),$student->getAttributes())) {
            echo 'not adding options for standard field';
            exit;
        }

        if(!$field = Field::where('institution_id',$campaign->institution_id)->where('name',$input_name)->first()) {
            $fh = new FieldHelper();
            $field = $fh->create_field($input_name, $campaign);
        }

        //Add new result
        $option_names = [];
        foreach($request['options'] as $option_name) {
            array_push($option_names, $option_name);
            if(!Option::where('field_id',$field->id)->where('name',$option_name)->first()) {
                $option = new Option();
                $option->field_id = $field->id;
                $option->name = $option_name;
                $option->save();
            }
        }

        //Delete options that no longer exist
        if(count($option_names) > 0) {
            Option::whereNotIn('name',$option_names)->where('field_id',$field->id)->delete();
        }

        echo 'saved';
    }

    private function format_domain($domain)
    {
        $domain = str_replace('http://', '', $domain);
        $domain = str_replace('https://', '', $domain);
        $domain = str_replace('www.', '', $domain);
        return $domain;
    }

    public function get_visitor_ip()
    {
        $client = @\Request::server('HTTP_CLIENT_IP');
        $forward = @\Request::server('HTTP_X_FORWARDED_FOR');
        $remote = @\Request::server('REMOTE_ADDR');

        if (filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif (filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        elseif ($remote == '::1')
        {
            //Dev IP
            $ip = '24.14.206.100';
        }
        elseif ($remote)
        {
            $ip = $remote;
        }
        else
        {
            $ip = '24.14.206.100';
        }

        return $ip;
    }

}
