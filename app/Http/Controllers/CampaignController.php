<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Field;
use App\FieldStudent;
use App\Helpers\CampaignHelper;
use App\Helpers\MailChimpHelper;
use App\Helpers\StudentHelper;
use App\Result;
use App\Student;
use App\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Config;

class CampaignController extends Controller
{


    public function set(Campaign $campaign)
    {
        session(['campaign' => $campaign]);

        if (\Request::input('goto')) {
            return redirect(\Request::input('goto'));
        } else {
            return redirect('campaign/students');
        }

    }

    public function index()
    {

        $campaigns = Campaign::where('institution_id', session('institution')->id)
            ->with('institution')
            ->get();

        if($campaigns->count() == 0) {
            return redirect(route('campaigns.create'));
        }

        $status = '';

        foreach ($campaigns as $campaign) {

            $status[$campaign->id] = '0';

//            //Connect to MailChimp
//            $mc = new MailChimpHelper($campaign);
//
//            //$mc->add_subscriber('mthomas@purlem.com');
//
//            //Find/Create List
//            $list_id = $mc->get_or_create_list();
//
//            //Find/Create folder for Institution
//            //$folder_id = $mc->get_or_create_campaign_folder();
//
//            //Manually create automation campaigns
//
//            //Check to make sure automation campaigns are created
//            if(!$mc->check_for_campaigns()) {
//                $status[$campaign->id] = 'Missing MailChimp campaign(s)';
//            }


        }

        return view('campaign/home')
            ->withCampaigns($campaigns)
            ->with('status', $status);
    }

    public function create(Institution $institution)
    {
        return view('campaign/create')->withInstitution($institution);
    }

    public function store(Request $request)
    {

        $directory = preg_replace("/[^a-zA-Z0-9]/i", "", $request['name']);

        $this->validate($request, [
            'name' => 'required|string|max:99|unique:campaigns'
        ]);

        $ch = new CampaignHelper();

        $name = preg_replace("/[^A-Za-z0-9 ]/", "", $request['name']);

        //Save campaign
        $campaign = new Campaign();
        $campaign->institution_id = session('institution')->id;
        $campaign->name = $name;
        if(!$request['ftp_server']) {
            $campaign->domain = $ch->cleanDomain($_SERVER["HTTP_HOST"]);
            $campaign->directory = 'storage/campaigns/'.$directory;
        } else {
            $campaign->directory = $directory;
            $campaign->domain = $ch->cleanDomain($request['domain']);
            $campaign->ftp_server = $request['ftp_server'];
            $campaign->ftp_username = $request['ftp_username'];
            $campaign->ftp_path = $request['ftp_path'];
            $campaign->save();
        }
        $campaign->save();


        //Upload LP Files
        $ch->install_lp_files($directory, $campaign, $request['ftp_server'], $request['ftp_username'], $request['ftp_password'], $request['ftp_path'], $directory);

        //Create Standard Fields
        $sh = new StudentHelper();
        $default_fields = ['Major','Extracurricular','Location'];

        foreach($default_fields as $default_field) {
            if(Field::where('institution_id',session('institution')->id)->where('name',$default_field)->first())
                continue;

            $field = new Field();
            $field->institution_id = session('institution')->id;
            $field->name = $default_field;
            $field->tag = $sh->format_field_for_mailchimp_tag($default_field);
            $field->save();
        }

        //Add List & Merge tags to MailChimp
        $mh = new MailChimpHelper($campaign);
        $mh->get_or_create_list();
        $mh->update_merge_fields();

        return redirect("/campaigns/set/$campaign->id");
    }


    public function show(Institution $institution)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Institution $institution, Campaign $campaign, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'ftp_username' => 'required|string|max:255',
            'ftp_path' => 'required|string|max:255',
        ]);

        $campaign->name = $request->name;
        $campaign->domain = $request->domain;
        $campaign->ftp_username = $request->ftp_username;
        $campaign->ftp_path = $request->ftp_path;
        $campaign->redirect_page = $request->redirect_page;
        $campaign->last_page = $request->last_page;
        $campaign->save();

        session(['campaign' => $campaign]);

        return redirect("/campaign/settings?message=Campaign+Updated");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Institution $institution, Campaign $campaign)
    {
        $campaign->delete();
        return redirect("/campaigns?message=Campaign+Deleted");
    }

    public function reset(Institution $institution, Campaign $campaign)
    {
        Student::where('campaign_id',$campaign->id)->delete();
        Result::where('campaign_id',$campaign->id)->delete();

        return redirect("/campaigns?message=Contacts+and+Results+Deleted");
    }


    public function settings(Institution $institution, Campaign $campaign)
    {
        return view('campaign/settings')
            ->withInstitution($institution)
            ->withCampaign($campaign);
    }

    public function results()
    {
        $students = Student::where('campaign_id', session('campaign')->id)->count();

        $visits = Result::selectRaw('count(DISTINCT(student_id)) as num')
            ->where('campaign_id',session('campaign')->id)
            ->where('page','home')
            ->first();
        $visits = $visits->num;

        $completed = Student::where('campaign_id', session('campaign')->id)->where('converted',1)->count();

        $visits_total = Result::selectRaw('page, count(*) as num')
            ->where('campaign_id',session('campaign')->id)
            ->where('page','<>','link')
            ->groupBy('page')
            ->onlyConverted()
            ->get();

        $visits_by_day = Result::selectRaw('page, DATE(created_at) as day, count(*) as num')
            ->where('campaign_id',session('campaign')->id)
            ->groupBy(\DB::raw('CONCAT(page,DATE(created_at))'))
            ->where('page','<>','link')
            ->orderBy('id','asc')
            ->onlyConverted()
            ->get();

        $days_for_chart = [];
        $page_visits_for_chart = [];
        foreach($visits_by_day as $visit_by_day) {
            if(!in_array($visit_by_day->day,$days_for_chart)) array_push($days_for_chart,$visit_by_day->day);

            if(!isset($page_visits_for_chart[$visit_by_day->page])) {
                $page_visits_for_chart[$visit_by_day->page] = [];
            }

            $page_visits_for_chart[$visit_by_day->page] = $page_visits_for_chart[$visit_by_day->page] + [
                $visit_by_day->day => $visit_by_day->num
            ];
        }

        $links = Result::selectRaw('url, count(*) as num')
            ->where('campaign_id',session('campaign')->id)
            ->where('page','link')
            ->groupBy('url')
            ->onlyConverted()
            ->get();

        //$fields = Field::where('institution_id',session('institution')->id)->has('students')->get(); //breaks php 7.3
        $do_not_include = ['id','firstName','lastName','email','purl'];
        $fields = Field::where('institution_id',session('institution')->id)->whereNotIn('name',$do_not_include)->get();

        $polls = [];
        foreach($fields as $field) {

            //Get values
            $values = FieldStudent::selectRaw('value, count(*) as num')
                ->join('students','students.id','=','field_student.student_id')
                ->where('field_id',$field->id)
                ->where('students.campaign_id',session('campaign')->id)
                ->where('value','<>','')
                ->groupBy('value')
                ->orderBy('num','desc')
                ->onlyConverted()
                ->get();
            $polls[$field->tag] = $values->toArray();

        }

        return view('campaign/results')
            ->with('students',$students)
            ->with('visits',$visits)
            ->with('completed',$completed)
            ->with('visits_total',$visits_total)
            ->with('page_visits_for_chart',$page_visits_for_chart)
            ->with('days_for_chart',$days_for_chart)
            ->withLinks($links)
            ->with('fields',$fields)
            ->with('polls',$polls);
    }

}
