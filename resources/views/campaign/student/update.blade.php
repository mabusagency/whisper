@extends('layouts.app')

@section('title')
    @include('includes.campaigns-menu')
@endsection

@section('panel-title')
    Update Student
@endsection

@section('panel-content')
    <form class="form-horizontal" role="form" method="POST"
          action="{!! route('students.update',['id' => $student->id]) !!}">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <div class="row">
            <div class="col-md-8">

                @foreach(config('app.roles') as $role)
                    <div class="form-group">
                        <label for="staff" class="col-md-2 control-label">{!! ucfirst($role) !!}</label>
                        <div class="col-md-10">
                            <select class="form-control" id="staff" class="form-control"
                                    name="{!! str_replace(' ','',strtolower($role)) !!}">
                                <option></option>
                                @foreach($institution->staff->where('role',$role) as $staff)
                                    <option value="{!! $staff->id !!} "
                                            @if($student->staff->contains($staff->id)) selected @endif>{!! $staff->name !!}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endforeach

                <hr/>

                <div class="form-group">
                    <label for="purl1" class="col-md-2 control-label">PURL</label>
                    <div class="col-md-5">
                        <input id="purl1" type="text" class="form-control" name="purl1"
                               value="{{ old('purl1',  $student->purl1) }}" required autofocus>
                    </div>
                    <div class="col-md-5">
                        <input id="purl2" type="text" class="form-control" name="purl2"
                               value="{{ old('purl2',  $student->purl2) }}" required>
                    </div>
                </div>

                <hr/>

                <div class="form-group">
                    <label for="firstName" class="col-md-2 control-label">First Name</label>
                    <div class="col-md-10">
                        <input id="firstName" type="text" class="form-control" name="firstName"
                               value="{{ old('firstName',  $student->firstName) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="lastName" class="col-md-2 control-label">Last Name</label>
                    <div class="col-md-10">
                        <input id="lastName" type="text" class="form-control" name="lastName"
                               value="{{ old('lastName', $student->lastName) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="col-md-2 control-label">Email</label>
                    <div class="col-md-10">
                        <input id="email" type="email" class="form-control" name="email"
                               value="{{ old('email', $student->email) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone" class="col-md-2 control-label">Phone</label>
                    <div class="col-md-10">
                        <input id="phone" type="text" class="form-control" name="phone"
                               value="{{ old('phone', $student->phone) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address" class="col-md-2 control-label">Address</label>
                    <div class="col-md-10">
                        <input id="address" type="text" class="form-control" name="address"
                               value="{{ old('address', $student->address) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address" class="col-md-2 control-label">Address 2</label>
                    <div class="col-md-10">
                        <input id="address2" type="text" class="form-control" name="address2"
                               value="{{ old('address2', $student->address2) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="city" class="col-md-2 control-label">City</label>
                    <div class="col-md-10">
                        <input id="city" type="text" class="form-control" name="city"
                               value="{{ old('city', $student->city) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="state" class="col-md-2 control-label">State</label>
                    <div class="col-md-10">
                        <input id="state" type="text" class="form-control" name="state"
                               value="{{ old('state', $student->state) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="zip" class="col-md-2 control-label">Zip</label>
                    <div class="col-md-10">
                        <input id="zip" type="text" class="form-control" name="zip"
                               value="{{ old('zip', $student->zip) }}">
                    </div>
                </div>

                <hr/>

                @foreach (\App\Field::where('institution_id',session('institution')->id)->get() as $field)
                    <div class="form-group">
                        <label for="field_{!! $field->id !!}"
                               class="col-md-2 control-label">{!! $field->name !!}</label>
                        <div class="col-md-10">
                            <input id="field_{!! $field->id !!}" type="text" class="form-control"
                                   name="field_{!! $field->id !!}" value="{!! $fields[$field->id] !!}">
                        </div>
                    </div>
                @endforeach

                <div class="form-group">
                    <div class="col-md-10 col-md-offset-2">
                        <button type="submit" class="btn btn-warning">
                            Update
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">


                @if(Auth::user()->type == 'staff')

                    <div class="form-group form-check">
                        <div class="col-md-12">
                            <input id="status" value="1" name="status" type="checkbox" class="form-check-input"
                                   @if($student->status == 1)checked="checked"@endif> &nbsp;
                            <label for="status" class="control-label">Contacted</label>
                        </div>
                    </div>
                    <hr/>

                    <div class="well">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label for="note">Notes:</label>
                                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-warning">
                                            Save
                                        </button>
                                    </div>
                                </div>

                                @foreach($student->notes as $note)
                                    <hr/>
                                    <b>{!! $note->created_at->format('M d \'y') !!}: </b>
                                    {!! $note->content !!}
                                    <br/>
                                    <a href="{!! route('note.delete',[$student->id,$note->id]) !!}">Delete</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="well">
                    <div class="row">
                        <div class="col-md-12">
                            PURL:<a href="{!! \App\Helpers\StudentHelper::get_testing_purl_url($student) !!}"
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


    </form>

    <hr/>

    <div class="col-md-12 text-right">
        <a class="btn btn-danger" data-toggle="modal" data-target="#{!! $student->id !!}">
            Delete Student
        </a>
        @include('includes/delete-modal', ['id' => $student->id, 'object' => 'Student', 'name' => $student->firstName.' '.$student->lastName, 'uri' => "/campaign/students/$student->id"])
    </div>

@endsection
