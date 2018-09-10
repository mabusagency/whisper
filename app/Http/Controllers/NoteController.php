<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Helpers\MailChimpHelper;
use App\Note;
use App\Student;
use App\Field;
use App\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends AppController
{
    public function destroy(Request $request, Student $student, Note $note)
    {
        $note = Note::where('student_id',$student->id)->where('id',$note->id)->first();
        if($note) $note->delete();

        return redirect("campaign/students/".$student->id."?message=Note+Deleted");
    }


}
