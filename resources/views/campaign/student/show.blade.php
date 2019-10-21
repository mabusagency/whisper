@extends('layouts.app')

@section('title')
    @include('includes.campaigns-menu')
@endsection

@section('panel-title')
    Student
@endsection

@section('panel-content')

    <div class="row">
        <div class="col-md-8">

            <div class="row">
                <div class="col-md-2">
                    <label>Name</label>
                </div>
                <div class="col-md-10">
                    {!! $student->firstName.' '.$student->lastName !!}
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <label>Email</label>
                </div>
                <div class="col-md-10">
                    {!! $student->email !!}
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <label>Phone</label>
                </div>
                <div class="col-md-10">
                    {!! $student->phone !!}
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <label>Address</label>
                </div>
                <div class="col-md-10">
                    @if($student->address)
                        {!! $student->address !!}<br/>
                        @if($student->address2) {!! $student->address2 !!}<br/> @endif
                        {!! $student->city !!} {!! $student->state !!}, {!! $student->zip !!}
                    @endif
                </div>
            </div>

            @foreach (\App\Field::where('institution_id',session('institution')->id)->get() as $field)

                <div class="row">
                    <div class="col-md-2">
                        <label>{!! $field->name !!}</label>
                    </div>
                    <div class="col-md-10">
                        {!! $fields[$field->id] !!}
                    </div>
                </div>

            @endforeach

        </div>

        <div class="col-md-4">

            <div class="form-group form-check">
                <div class="col-md-12">
                    <input id="status" value="1" name="status" type="checkbox" class="form-check-input"
                           @if($student->status == 1)checked="checked"@endif disabled> &nbsp;
                    <label for="status" class="control-label">Contacted</label>
                </div>
            </div>
            <hr/>

            @if($student->notes->count() > 0)
            <div class="well">
                <div class="row">
                    <div class="col-md-12">
                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-12">--}}
                                {{--<label for="note">Notes:</label>--}}
                                {{--<textarea class="form-control" id="note" name="note" rows="3"></textarea>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-12">--}}
                                {{--<button type="submit" class="btn btn-warning">--}}
                                    {{--Save--}}
                                {{--</button>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        @foreach($student->notes as $note)
                            <b>{!! $note->created_at->format('m/d/Y') !!}: </b>
                            {!! $note->content !!}
                            <br/>
                            {{--<a data-toggle="modal" data-target="#{!! $note->id !!}" >--}}
                                {{--Delete--}}
                            {{--</a>--}}
                            @include('includes/delete-modal', ['id' => $note->id, 'object' => 'Note', 'name' => 'this note', 'uri' => route('note.delete',[$student->id,$note->id])])

                            <hr/>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="well">
                <div class="row">
                    <div class="col-md-12">
                        @foreach(config('app.roles') as $role)
                            @if($staff = $student->staff->where('role',$role)->first())
                                {!! ucfirst($role) !!}: {!! $staff->name !!}<br/>
                            @endif
                        @endforeach

                        PURL: <a href="{!! \App\Helpers\StudentHelper::get_testing_purl_url($student) !!}"
                                 target="_blank">
                            {!! \App\Helpers\StudentHelper::get_purl_url($student) !!}</a>
                    </div>
                </div>
            </div>

            {{--<div class="well">--}}
            {{--<div class="row">--}}
            {{--<div class="col-md-12">--}}
            {{--MailChimp ID: {!! str_limit($student->mailchimp_member_id,20) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}

        </div>
    </div>

    <hr/>

    <div class="pull-right">
        <a class="btn btn-danger" data-toggle="modal" data-target="#{!! $student->id !!}">
            Delete Student
        </a>
        @include('includes/delete-modal', ['id' => $student->id, 'object' => 'Student', 'name' => $student->firstName.' '.$student->lastName, 'uri' => "/campaign/students/$student->id"])
    </div>

    <a class="btn btn-warning" href="{!! route('students.edit',[$student->id]) !!}">
        Edit
    </a>




@endsection
