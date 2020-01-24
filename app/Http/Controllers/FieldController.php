<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Helpers\MailChimpHelper;
use App\Student;
use App\Field;
use App\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FieldController extends AppController
{

    public function index()
    {
        $fields = Field::where('institution_id',session('institution')->id)->get();
        return view('field/home')
            ->withFields($fields);
    }

    public function create()
    {
        return view('field/create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255|unique:fields',
            'tag' => 'required|string|max:10|unique:fields'
        ]);

        $field = new Field();
        $field->institution_id = session('institution')->id;
        $field->name = $request['name'];
        $field->tag = strtoupper($request['tag']);
        $field->results = $request['results'];
        $field->save();

        //Apply to every campaign
        $campaigns = Campaign::where('institution_id',session('institution')->id)->get();
        foreach($campaigns as $campaign) {
            $mh = new MailChimpHelper($campaign);
            $mh->update_merge_fields();
        }

        return redirect("/fields?message=Field+Created");
    }

    public function show(Field $field)
    {
        return view('field/update')
            ->withField($field);
    }

    public function update(Request $request, Field $field)
    {

        if($request['name'] != $field->name) {
            $this->validate($request, [
                'name' => 'required|string|max:255|unique:fields,name,'.$field->id,
                'tag' => 'required|string|max:10|unique:fields,tag,'.$field->id,
            ]);
        }

        $campaigns = Campaign::where('institution_id',session('institution')->id)->get();
        foreach($campaigns as $campaign) {
            $mh = new MailChimpHelper($campaign);
            $mh->rename_merge_field($field->tag, $request);
        }

        $field->name = $request['name'];
        $field->tag = strtoupper($request['tag']);
        $field->results = $request['results'];
        $field->save();

        return redirect("/fields?message=Field+Created");
    }

    public function destroy(Field $field)
    {
        //Apply to every instution campaign
        $campaigns = Campaign::where('institution_id',session('institution')->id)->get();
        foreach($campaigns as $campaign) {
            $mh = new MailChimpHelper($campaign);
            $mh->delete_merge_field($field->tag);
        }

        $field->delete();

        return redirect("/fields?message=Field+Deleted");
    }


}
