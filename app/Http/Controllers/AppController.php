<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Institution;
use App\Staff;
use App\Student;
use Ngocnh\Highchart\DataPoint\DataPoint;
use Ngocnh\Highchart\Series\LineSeries;
use Ngocnh\Highchart\Series\ScatterSeries;

class AppController extends Controller
{

    public $institution;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {

        //Staff
        if(\Auth::user()->type == 'staff') {
            $staff = Staff::where('user_id',\Auth::user()->id)->first();
            $institution = Institution::where('id',$staff->institution_id)->first();
            $campaign = Campaign::where('institution_id',$institution->id)->orderBy('id','desc')->first();
            session(['institution' => $institution]);
            session(['campaign' => $campaign]);
            return redirect('/campaigns');
        }
        //Institutions
        elseif(\Auth::user()->type == 'institution') {
            $institution = Institution::where('user_id',\Auth::user()->id)->first();
            session(['institution' => $institution]);
            return redirect('/campaigns');
        }
        //Admin
        else {
            return redirect('/institutions');
        }

    }

//    public function set_campaign(Campaign $campaign)
//    {
//        session(['campaign' => $campaign]);
//        return redirect('manager/campaign/results');
//    }
//
//    public function campaigns()
//    {
//        $campaigns = Campaign::where('institution_id', session('institution')->id)->get();
//        return view('campaign/home')
//            ->withCampaigns($campaigns);
//    }
//
//    public function results(Campaign $campaign) {
//        return view('app/campaign/results')
//            ->withCampaign($campaign);
//    }
//
//    public function students(Campaign $campaign)
//    {
//        $students = Student::where('campaign_id', session('campaign')->id)->get();
//        return view('app/campaign/student/home')
//            ->withCampaign($campaign)
//            ->withStudents($students);
//    }
//
//    public function staff()
//    {
//        $staff = Staff::where('institution_id', session('institution')->id)->get();
//
//        return view('staff/home')
//            ->withStaff($staff);
//    }

}
