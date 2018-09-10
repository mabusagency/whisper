<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Institution;
use App\User;
use Illuminate\Http\Request;
use Ngocnh\Highchart\DataPoint\DataPoint;
use Ngocnh\Highchart\Series\LineSeries;
use Ngocnh\Highchart\Series\ScatterSeries;

class InstitutionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function set(Institution $institution)
    {
        session(['institution' => $institution]);
        return redirect('campaigns');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $institutions = Institution::all();
        return view('institution/home')->withInstitutions($institutions);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('institution/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'contact' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
            'mailchimp_key' => 'required|string|max:255'
        ]);

        $user = new User();
        $user->email = $request['email'];
        $user->password = bcrypt($request['password']);
        $user->type = 'institution';
        $user->save();

        $institution = new Institution();
        $institution->user_id = $user->id;
        $institution->name = $request['name'];
        $institution->contact = $request['contact'];
        $institution->address = $request['address'];
        $institution->city = $request['city'];
        $institution->state = $request['state'];
        $institution->zip = $request['zip'];
        $institution->mailchimp_key = $request['mailchimp_key'];

        //Save Logo
        if ($request->file('logo') && $request->file('logo')->isValid()) {
            $destinationPath = 'uploads/logo'; // upload path
            $extension = $request->file('logo')->getClientOriginalExtension(); // getting csv extension
            $fileName = rand(11111, 99999) . '.' . $extension; // renameing csv
            $request->file('logo')->move($destinationPath, $fileName); // uploading file to given path
            $institution->logo = $fileName;
        }

        $institution->save();

        return redirect('/institutions/set/'.$institution->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Institution $institution)
    {
        return view('institution/update')->withInstitution($institution);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Institution $institution)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Institution $institution)
    {
        $user = $institution->user;

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
            'mailchimp_key' => 'required|string|max:255',
        ]);

        if($request['email'] != $user->email) {
            if(User::where('email',$request['email'])->where('id','<>',$user->id)->first()) {
                return redirect('/institutions/'.$institution->id.'?error=Email already exists');
            }
            $user->email = $request['email'];
        }

        if($request['password']) {
            $user->password = bcrypt($request['password']);
        }
        $user->save();

        $institution->name = $request['name'];
        $institution->contact = $request['contact'];
        $institution->address = $request['address'];
        $institution->city = $request['city'];
        $institution->state = $request['state'];
        $institution->zip = $request['zip'];
        $institution->mailchimp_key = $request['mailchimp_key'];

        //Save Logo
        if ($request->file('logo') && $request->file('logo')->isValid()) {
            $destinationPath = 'uploads/logo'; // upload path
            $extension = $request->file('logo')->getClientOriginalExtension(); // getting csv extension
            $fileName = rand(11111, 99999) . '.' . $extension; // renameing csv
            $request->file('logo')->move($destinationPath, $fileName); // uploading file to given path
            $institution->logo = $fileName;
        }

        $institution->save();

        return redirect('/institutions/'.$institution->id.'?message=Institution Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Institution $institution)
    {
        $user = $institution->user;
        $user->delete();

        return redirect('/institutions?message=Institution Deleted');
    }

}
