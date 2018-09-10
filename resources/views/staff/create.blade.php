@extends('layouts.app')

@section('content')
    <div class="alert alert-warning">
        Add a Staff member. They can be
        @foreach(config('app.roles') as $i => $staff)
            @if(count(config('app.roles'))-1 == $i)
                or {!! ucfirst($staff) !!}s.
            @else
                {!! ucfirst($staff) !!}s,
            @endif

        @endforeach
    </div>
@endsection

@section('panel-title')
    Add Staff
@endsection

@section('panel-content')
    <form class="form-horizontal" role="form" method="POST" action="{!! route('staff.store') !!}">
        {{ csrf_field() }}

        <div class="form-group">
            <label for="role" class="col-md-2 control-label">Role</label>

            <div class="col-md-6">
                <select class="form-control" id="role" class="form-control" name="role">
                    <option value="" disabled selected>Select role</option>
                    @foreach(config('app.roles') as $role)
                        <option @if(old('role') == $role) selected
                                @endif value="{!! $role !!}">{!! ucfirst($role) !!}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <hr/>

        <div class="form-group">
            <label for="name" class="col-md-2 control-label">Name</label>

            <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required
                       autofocus>
            </div>
        </div>

        <div class="form-group">
            <label for="email" class="col-md-2 control-label">E-Mail Address</label>

            <div class="col-md-6">
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
            </div>
        </div>

        <div id="password_section" style="display:none;">
            <div class="form-group">
                <label for="password" class="col-md-2 control-label">Password</label>

                <div class="col-md-6">
                    <input id="password" type="password" class="form-control" name="password">
                </div>
            </div>

            <div class="form-group">
                <label for="password-confirm" class="col-md-2 control-label">Confirm Password</label>

                <div class="col-md-6">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                </div>
            </div>
        </div>

        <hr/>

        <div class="form-group">
            <label for="area" class="col-md-2 control-label" style="padding-top:20px;">Student Assignment</label>

            <div class="col-md-6" id="rules">

                {{--<div class="alert alert-warning">--}}
                {{--Use the optional fields below to automatically assign this staff member to students.--}}
                {{--</div>--}}


                <div class="row" class="rule" style="margin-top:15px;">
                    <div class="col-md-3">
                        <select class="form-control" id="trigger" class="form-control" name="field_id">
                            <option value="" selected>County</option>
                            <option disabled>--------</option>
                            @foreach($fields as $field)
                                <option @if(old('field_id') == $field->id) selected
                                        @endif value="{!! $field->id !!}">{!! $field->name !!}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="field_inputs" style="display:none">
                        {{--<div class="col-md-3">--}}
                        {{--<select class="form-control" id="condition" class="form-control" name="operator">--}}
                        {{--@foreach(config('app.operators') as $operator)--}}
                        {{--@if(isset( $staff->rules->operator))--}}
                        {{--<option @if(old('operator', $staff->rules->operator) == $operator) selected @endif>{!! $operator !!}</option>--}}
                        {{--@else--}}
                        {{--<option @if(old('operator') == $operator) selected @endif>{!! $operator !!}</option>--}}
                        {{--@endif--}}
                        {{--@endforeach--}}
                        {{--</select>--}}
                        {{--</div>--}}
                        <div class="col-md-9">
                            {{--@if(isset( $staff->rules->operator))--}}
                            {{--<input id="value" type="text" class="form-control" name="value"--}}
                            {{--value="{!! old('value', $staff->rules->value) !!}">--}}
                            {{--@else--}}
                            {{--<input id="value" type="text" class="form-control" name="value"--}}
                            {{--value="{!! old('value') !!}">--}}
                            {{--@endif--}}
                            <div class="row">
                                @foreach($fields as $field)
                                    <div id="{!! $field->name !!}_options" class="field_options" @if(old('field_id') != $field->id) style="display:none" @endif>
                                        @foreach($field->options as $option)
                                            <div class="col-md-12">
                                                <input type="checkbox" name="options[]" class="option_checkbox"
                                                       value="{!! $option->id !!}"
                                                       @if(in_array($option->id,$usedOptions)) disabled @endif >&nbsp;
                                                {!! $option->name !!}
                                                @if(in_array($option->id,$usedOptions))
                                                    <span style="font-size:12px;">(<a href="{!! route('staff.update',[ $option->staff->first()->id]) !!}">{!! $option->staff->first()->name !!}</a>)</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9" id="counties_input">

                        {{--@if($staff->counties)--}}
                        {{--<div style="padding-bottom:10px;">--}}
                        {{--@foreach($staff->counties as $county)--}}
                        {{--<div class="label label-default" style="background-color:#dcdcdc;color:#636b6f;margin-right:4px;">--}}
                        {{--<span class="glyphicon glyphicon-remove"></span>--}}
                        {{--{!! $county->name !!}--}}
                        {{--</div>--}}
                        {{--@endforeach--}}
                        {{--</div>--}}
                        {{--@endif--}}

                        <div class="form-group">
                            <div class="col-md-12">
                                <input id="search" type="text" class="form-control" name="search"
                                       placeholder="search for state">
                            </div>
                        </div>

                        <div style="height: 300px; overflow-y: scroll;">
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                @foreach(\App\State::all() as $state)
                                    <div class="panel panel-default" id="{!! $state->abbr !!}">
                                        <div class="panel-heading" role="tab" id="heading{!! $state->abbr !!}">
                                            <h4 class="panel-title" state="{!! $state->abbr !!}">
                                                <input type="checkbox" class="stateCheckbox" id="{!! $state->id !!}"
                                                       name="{!! $state->abbr !!} ">&nbsp;
                                                <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                   href="#collapse{!! $state->abbr !!}" aria-expanded="true"
                                                   aria-controls="collapse{!! $state->abbr !!}">
                                                    {!! $state->name !!}
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse{!! $state->abbr !!}" class="panel-collapse collapse in"
                                             role="tabpanel" aria-labelledby="heading{!! $state->abbr !!}">
                                            <div class="list-group">
                                                @foreach($state->counties as $county)
                                                    <li class="list-group-item">
                                                        <input type="checkbox" name="counties[]"
                                                               value="{!! $county->id !!}" class="{!! $state->abbr !!}"
                                                               @if(in_array($county->id,$usedCounties)) disabled @endif>&nbsp; {!! $county->name !!}

                                                        @if(in_array($county->id,$usedCounties))
                                                            <span style="font-size:12px;">(<a href="{!! route('staff.update',[ $county->staff->first()->id]) !!}">{!! $county->staff->first()->name !!}</a>)</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <hr/>

        <div class="form-group">
            <div class="col-md-6 col-md-offset-2">
                <button type="submit" class="btn btn-warning">
                    Create
                </button>
            </div>
        </div>
    </form>

@endsection

@section('foot')
    <script src="{{ asset('js/staff.js') }}"></script>
@endsection
