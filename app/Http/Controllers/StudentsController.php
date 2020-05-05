<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Field;
use App\FieldStudent;
use App\Helpers\MailChimpHelper;
use App\Helpers\StudentHelper;
use App\Institution;
use App\Note;
use App\Result;
use App\Staff;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class StudentsController extends Controller
{

    public function __construct()
    {

    }

    public function index()
    {
//        $staff = Staff::where('institution_id', session('institution')->id)->first();
//        if(!$staff) {
//            return redirect('/staff/create');
//        }

        $error = [];
        $info = '';
        $message = '';

        //Connect to MailChimp
        if(\Auth::user()->type == 'admin') {

            //Check to make sure automation campaigns are created
            $mc = new MailChimpHelper(session('campaign'));
            if (!$mc->check_for_campaign())
                array_push($error, '<img src="/images/freddie.png" height="30" />&nbsp;&nbsp;&nbsp;<a href="https://share.nuclino.com/p/Creating-MailChimp-Campaigns-z34hqfc97mf9fIYxL8QcKt" target="_blank">Create MailChimp Campaign</a>');
        }

        if($search = \Request::input('query')) {
            $students = Student::where('campaign_id', session('campaign')->id)
                ->where(function ($query) use ($search) {
                    $query->where('firstName', 'like', '%' . $search . '%')
                        ->orWhere('lastName', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%')
                        ->orWhere('address2', 'like', '%' . $search . '%')
                        ->orWhere('city', 'like', '%' . $search . '%')
                        ->orWhere('state', 'like', '%' . $search . '%')
                        ->orWhere('zip', 'like', '%' . $search . '%');
                })
                ->filter()
                ->orderBy('updated_at', 'desc')
                ->get();

            if ($students->count() == 0) {
                return redirect("/campaign/students?error=No results");
            } else {
                $message = 'found ' . $students->count() . ' students';
            }
        } else {
            $students = Student::where('campaign_id', session('campaign')->id)
                ->filter()
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        if(\Request::input('page') || \Request::input('notes') || \Request::input('contacted') || \Request::input('deleted')) {
            $message = '<div style="float:right;"><a href="/campaign/students">clear</a></div>'.$students->count().' students ';
        } else {
            if($students->count() < 3 && Auth::user()->type == 'admin') {
                $info = 'Next Step: <a href="https://share.nuclino.com/p/Upload-Contacts-4_bKreNAod6Phoyod-ob26">Upload Contacts</a>';
            }
        }

        $and = '';
        if($pages = \Request::input('page')) {
            foreach($pages as $i => $page) {
                if(substr($page,0,1) == '-') {
                    $page = str_replace('-','',$page);
                    $message .= $and.'<u>did not</u> visit the <b>'.$page.'</b> page.';
                } else {
                    $message .= $and.'visited the <b>'.$page.'</b> page.';
                }
                $and = ' And ';
            }
        }

        if(\Request::input('contacted') == 'Y') {
            $message .= $and.'has been contacted.';
        } elseif(\Request::input('contacted') == 'N') {
            $message .= $and.'have not been contacted.';
        }

        if(\Request::input('notes') == 'Y') {
            $message .= $and.'have notes.';
        } elseif(\Request::input('notes') == 'N') {
            $message .= $and.'does not have notes.';
        }

        $pages= [];
        $results = Result::select('page')
            ->where('campaign_id', session('campaign')->id)
            ->where('page','<>','link')
            ->groupBy('page')
            ->get();
        foreach($results as $result) {
            array_push($pages,$result->page);
        }

        $query_string = '';
        if(isset($_SERVER['QUERY_STRING'])) {
            $query_string = '?'.$_SERVER['QUERY_STRING'];
        }


        return view('campaign/student/home')
            ->withStudents($students)
            ->withPages($pages)
            ->withError($error)
            ->withInfo($info)
            ->withMessage($message)
            ->with('query_string',$query_string);
    }

    public function create()
    {
        return view('campaign/student/create');
    }

    public function store(Request $request)
    {

        $sh = new StudentHelper();

        $this->validate($request, [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'email'
        ]);

        if($request['email'] && Student::where('institution_id',session('institution')->id)->where('email',$request['email'])->first()) {
            Session::flash('message', 'Email already in use');
            Session::flash('alert-class', 'alert-danger');
            return back()->withInput();
        }

        $student = new Student();
        $student->institution_id = session('institution')->id;
        $student->campaign_id= session('campaign')->id;
        $student->purl1 = $request['firstName'];
        $student->purl2 = $request['lastName'];
        $student->firstName = $request['firstName'];
        $student->lastName = $request['lastName'];
        $student->email = $request['email'];
        $student->phone = $request['phone'];
        $student->address = $request['address'];
        $student->address2 = $request['address2'];
        $student->city = $request['city'];
        $student->state = $request['state'];
        $student->zip = $request['zip'];
        $student->save();

        foreach (session('institution')->fields as $field) {
            if ($request['field_'.$field->id]) {
                if(!$fs = FieldStudent::where('field_id',$field->id)->where('student_id',$student->id)->first()) {
                    $fs = new FieldStudent();
                    $fs->student_id = $student->id;
                    $fs->field_id = $field->id;
                }
                $fs->value = trim($request['field_'.$field->id]);
                $fs->save();
            }
        }

        StudentHelper::match_to_staff($student);

        $student = $sh->make_purl_unique($student);

        //Add Contact to MailChimp List
        if($student->email) {
            $mh = new MailChimpHelper(session('campaign'));
            $mh->add_student($student);
        }
        return redirect("/campaign/students?message=Student+Created");
    }

    public function show(Request $request, Student $student)
    {
        $sh = new StudentHelper();

        $fields = $sh->get_student_custom_fields($student);
        $institution = Institution::find(session('institution')->id);

        if($request->input('show_mailchimp_data')) {
            $mh = new MailChimpHelper(session('campaign'));
            $studnet = $mh->get_student($student);
            dd($student);
        }

        return view('campaign/student/show')
            ->withStudent($student)
            ->withFields($fields)
            ->withInstitution($institution);
    }

    public function edit(Student $student)
    {
        $sh = new StudentHelper();

        $fields = $sh->get_student_custom_fields($student);
        $institution = Institution::find(session('institution')->id);
        return view('campaign/student/update')
            ->withStudent($student)
            ->withFields($fields)
            ->withInstitution($institution);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        $this->validate($request, [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'email',
        ]);

        $original_email = $student->email;

        $student->purl1 = $request['purl1'];
        $student->purl2 = $request['purl2'];
        $student->firstName = $request['firstName'];
        $student->lastName = $request['lastName'];
        $student->email = $request['email'];
        $student->phone = $request['phone'];
        $student->address = $request['address'];
        $student->address2 = $request['address2'];
        $student->city = $request['city'];
        $student->state = $request['state'];
        $student->zip = $request['zip'];
        $student->status = $request['status'];
        $student->save();

        foreach (Field::where('institution_id',session('institution')->id)->get() as $field) {

            if ($request['field_'.$field->id]) {
                if(!$fs = FieldStudent::where('field_id',$field->id)->where('student_id',$student->id)->first()) {
                    $fs = new FieldStudent();
                    $fs->student_id = $student->id;
                    $fs->field_id = $field->id;
                }
                $fs->value = trim($request['field_'.$field->id]);
                $fs->save();
            }
        }

        $sh = new StudentHelper();
        $sh->make_purl_unique($student);

        //Match to Staff
        $student->staff()->detach();
        foreach(config('app.roles') as $role) {
            if($request[$role]) {
                $student->staff()->attach($request[$role]);
            }
        }

        //Assign Note
        if($request['note'] && $request['note'] != '') {
            $note = new Note();
            $note->student_id = $student->id;
            $note->content = $request['note'];
            $note->save();
        }

        //Update MailChimp
        $mh = new MailChimpHelper(session('campaign'));
        $response = $mh->update_student($student, $original_email);
        $error = '';
        if(isset($response['status']) && $response['status'] == '400') {
            $error = $response['detail'];
        }

        return redirect("/campaign/students/$student->id?message=Contact+Updated&mailchimp_error=$error");
    }

    public function destroy(Student $student)
    {
        $mh = new MailChimpHelper(session('campaign'));
        $mh->destroy_member($student);

        $student->delete();
        return redirect("/campaign/students?message=Student+Deleted");
    }

    public function restore($student_id)
    {
        $student = Student::withTrashed()->find($student_id);
        if($student) {
            $student->restore();
        }
        return redirect("/campaign/students/".$student->id."?message=Student+Restored");
    }


    public function settings()
    {
        return view('campaign/settings');
    }

    public function results()
    {
        return view('campaign/results');
    }


    public function export()
    {
        $sh = new StudentHelper();

        header('Content-Disposition: attachment; filename="export.csv"');
        header("Cache-control: private");
        header("Content-type: application/force-download");
        header("Content-transfer-encoding: binary\n");

        $students = Student::where('campaign_id', session('campaign')->id)->filter()->get();

        $csv = [];

        foreach($students as $student) {

            $purl = $sh->get_purl_url($student);

            $row = [
                'id' => $student->id,
                'purl' => $purl,
                'firstName' => $student->firstName,
                'lastName' => $student->lastName,
                'email' => $student->email,
                'phone' => $student->phone,
                'address' => $student->address,
                'address2' => $student->address2,
                'city' => $student->city,
                'state' => $student->state,
                'zip' => $student->zip,
            ];

            $student_fields = $sh->get_student_custom_fields($student);
            foreach (Field::where('institution_id',session('institution')->id)->get() as $field) {
                $row['_'.$field->name] = $student_fields[$field->id];
            }

            array_push($csv, $row);

        }


        $out = fopen('php://output', 'w');

        fputcsv($out, array_keys($csv[1]));

        foreach($csv as $line)
        {
            fputcsv($out, $line);
        }
        fclose($out);

    }

    public function upload_preview(Request $request)
    {
        if(!$request['file']) {
            $csv_file_path = $this->upload_csv($request);
        } else {
            $csv_file_path = $request['file'];
        }

        $preview_data = $this->get_csv_data($csv_file_path, 3);

        if (!$preview_data)
            return \Redirect::to('/dashboard/contact/upload')->withMessage('Upload file was not found');

        return \View::make('campaign.student.upload')->withCsv_file($csv_file_path)->withPreview_data($preview_data);
    }


    private function upload_csv($request)
    {
        $request->flash();

        $this->validate($request, [
            'csv' => 'required',
        ]);

        //Save CSV
        if ($request->file('csv') && $request->file('csv')->isValid()) {
            $destinationPath = 'uploads/csv'; // upload path
            $extension = $request->file('csv')->getClientOriginalExtension(); // getting csv extension
            $fileName = rand(11111, 99999) . '.' . $extension; // renameing csv
            $request->file('csv')->move($destinationPath, $fileName); // uploading file to given path
        }

        return $destinationPath . '/' . $fileName;
    }

    private function get_csv_data($csv_file_path, $num_rows = null)
    {

        if (!$num_rows) {
            $num_rows = count(file($csv_file_path, FILE_SKIP_EMPTY_LINES));
        }

        setlocale(LC_ALL, 'en_US.utf-8'); //required so that first characters with accents are read correctly.
        ini_set('auto_detect_line_endings', true); //allows multiple types of csv formats to be used.

        $data = [];
        if (($handle = fopen($csv_file_path, "r")) !== false) {
            $i = 0;
            while (($row = fgetcsv($handle, 10000, ",")) !== false) {
                $data[$i] = $row;
                if ($i == $num_rows)
                    break;
                $i++;
            }
            fclose($handle);
        }


        return $data;
    }

    public function upload_execute(Request $request)
    {
        $sh = new StudentHelper();
        $mh = new MailChimpHelper(session('campaign'));

        $custom_fields = Field::where('institution_id', session('institution')->id)->get();

        $custom_field_tags = [];
        foreach ($custom_fields as $custom_field) {
            array_push($custom_field_tags, $custom_field->tag);
        }

        //Identify the matched field name for each column
        $column = [];
        foreach ($request->columns as $id => $name) {

            $tag = strtoupper($name);

            //While your at it, create new custom field if the column does not exist
            if (!in_array(strtolower($name), $sh->standard_field_names) && !in_array($tag, $custom_field_tags)) {

                //Make its unique if it already exists
                if(Field::where('institution_id',session('institution')->id)->where('tag',$tag)->first()) {
                    $tag = substr($tag,0,8).rand(10,99);
                }

                $field = new Field();
                $field->institution_id = session('institution')->id;
                $field->name = $name;
                $field->tag = $tag;
                $field->save();
            }

            $column[$name] = $id;
        }

        if(!array_key_exists('firstName',$column) || !array_key_exists('lastName',$column)) {
            return \Redirect::to(route('students.upload.preview').'?error=First+and+Last+names+requried&file='.urlencode($request->csv_file))->withMessage('Contacts Uploaded');
        }

        //Add uploaded and matched contacts into database
        $rows = $this->get_csv_data($request->csv_file);

        foreach ($rows as $row) {

            if (strstr(strtolower($row[$column['firstName']]), 'first')) continue;

            if(!$row[$column['firstName']] || !$row[$column['lastName']]) continue;

            $student = new Student();
            $student->institution_id = session('institution')->id;
            $student->campaign_id = session('campaign')->id;
            $student->purl1 = $row[$column['firstName']];
            $student->purl2 = $row[$column['lastName']];

            foreach ($column as $field => $id) {
                if (isset($column[$field])
                    && isset($row[$column[$field]])
                    && in_array($field, $sh->standard_field_names)
                ) {
                    $student->$field = $row[$column[$field]];
                }
            }

            $student->save();

            //Save custom fields
            foreach ($custom_fields as $field) {

                $tag_from_post = strtolower($field->tag);

                if (isset($column[$tag_from_post]) && isset($row[$column[$tag_from_post]])) {
                    $fs = new FieldStudent();
                    $fs->student_id = $student->id;
                    $fs->field_id = $field->id;
                    $fs->value = $row[$column[$tag_from_post]];
                    $fs->save();
                }
            }

            $student = $sh->make_purl_unique($student);

            //Match to Staff
            $student = StudentHelper::match_to_staff($student);

            //Add Contact to MailChimp List
            if($student->email) {
                $mh->add_student($student);
            }

        }

        //redirect
        return \Redirect::to(route('students.index'))->withMessage('Contacts Uploaded');
    }

}
