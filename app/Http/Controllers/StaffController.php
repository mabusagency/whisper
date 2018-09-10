<?php

namespace App\Http\Controllers;

use App\Area;
use App\Campaign;
use App\CountyStaff;
use App\Field;
use App\Staff;
use App\StaffOptions;
use App\Student;
use App\Institution;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('manager');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Institution $institution)
    {
        $staff = Staff::where('institution_id', session('institution')->id)->get();

        if($staff->count() == 0) {
            return redirect(route('staff.create'));
        }

        return view('staff/home')
            ->withInstitution($institution)
            ->withStaff($staff);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Institution $institution, $id)
    {
        $staff = Staff::where('institution_id',session('institution')->id)->find($id);
        $fields = Field::where('institution_id',session('institution')->id)
            ->has('options')
            ->get();

        $state_ids = [];
        $counties = [];
        foreach($staff->counties as $county) {
            array_push($counties,$county->id);
            if(!in_array($county->state_id,$state_ids)) {
                array_push($state_ids,$county->state_id);
            }
        }

        $full_states = [];
        $partial_states = [];
        foreach(\App\State::whereIn('id',$state_ids)->get() as $state) {
            $all_counties = true;
            foreach($state->counties as $county) {
                if(!in_array($county->id,$counties)) {
                    $all_counties = false;
                    array_push($partial_states,$state->id);
                    break;
                }
            }
            if($all_counties) {
                array_push($full_states,$state->id);
            }
        }

        $staffField = '';
        $staffOptions = [];
        if($staff->options->count() > 0) {
            $staffField = $staff->options->first()->field->id;
            foreach($staff->options as $option) {
                array_push($staffOptions, $option->id);
            }
        }

        $usedOptions = [];
        foreach(StaffOptions::where('institution_id',session('institution')->id)->where('staff_id','<>',$staff->id)->get() as $so) {
            array_push($usedOptions, $so->option_id);
        }

        $usedCounties = [];
        foreach(CountyStaff::where('institution_id',session('institution')->id)->where('staff_id','<>',$staff->id)->get() as $cs) {
            array_push($usedCounties, $cs->county_id);
        }

        return view('staff/update')
            ->withInstitution($institution)
            ->withStaff($staff)
            ->withFields($fields)
            ->withCounties($counties)
            ->with('fullStates',$full_states)
            ->with('partialStates',$partial_states)
            ->with('staffOptions',$staffOptions)
            ->with('staffField',$staffField)
            ->with('usedOptions',$usedOptions)
            ->with('usedCounties',$usedCounties);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Institution $institution)
    {
        $fields = Field::where('institution_id',session('institution')->id)
            ->has('options')
            ->get();

        $usedOptions = [];
        foreach(StaffOptions::where('institution_id',session('institution')->id)->get() as $so) {
            array_push($usedOptions, $so->option_id);
        }

        $usedCounties = [];
        foreach(CountyStaff::where('institution_id',session('institution')->id)->get() as $cs) {
            array_push($usedCounties, $cs->county_id);
        }

        return view('staff/create')
            ->withInstitution($institution)
            ->withFields($fields)
            ->with('usedOptions',$usedOptions)
            ->with('usedCounties',$usedCounties);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request['role'] == 'recruiter') {
            $this->validate($request, [
                'role' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $user = new User();
            $user->email = $request['email'];
            $user->password = bcrypt($request['password']);
            $user->type = 'staff';
            $user->save();
        } else {
            $this->validate($request, [
                'role' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
            ]);

            $user = Auth::user();
        }

        $staff = new Staff();
        $staff->institution_id = session('institution')->id;
        $staff->user_id = $user->id;
        $staff->name = $request['name'];
        $staff->role = $request['role'];
        $staff->save();

        //Assign to options
        if($request['options'] && is_array($request['options'])) {
            foreach($request['options'] as $option_id) {
                $staff->options()->attach($option_id, ['institution_id' => session('institution')->id]);
            }
        }

        //Attach counties
        if($request['counties'] && is_array($request['counties'])) {
            foreach($request['counties'] as $county_id) {
                $staff->counties()->attach($county_id, ['institution_id' => session('institution')->id]);
            }
        }

        return redirect('/staff?message=Staff Created');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Staff $staff)
    {

        $user = User::find($staff->user_id);
        $institution = Institution::find(session('institution')->id);

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        if($request['email'] != $user->email) {
            if(User::where('email',$request['email'])->where('id','<>',$user->id)->first()) {
                return redirect('/staff/'.$staff->id.'?error=Email already exists');
            }
            $user->email = $request['email'];
        }

        if($request['password']) {
            $user->password = bcrypt($request['password']);
        }
        $user->save();

        $staff->name = $request['name'];
        $staff->role = $request['role'];
        $staff->save();

        //Assign to options
        $staff->options()->detach();
        if($request['options'] && is_array($request['options'])) {
            foreach($request['options'] as $option_id) {
                $staff->options()->attach($option_id, ['institution_id' => $institution->id]);
            }
        }

        //Attach counties
        $staff->counties()->detach();
        if($request['counties'] && is_array($request['counties'])) {
            foreach($request['counties'] as $county_id) {
                $staff->counties()->attach($county_id, ['institution_id' => $institution->id]);
            }
        }

        //Update session
        session(['institution' => $institution]);

        return redirect('/staff/'.$staff->id.'?message=Staff Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Staff $staff)
    {
        $user = $staff->user;
        $user->delete();

        return redirect('/staff?message=Staff Deleted');
    }

}
