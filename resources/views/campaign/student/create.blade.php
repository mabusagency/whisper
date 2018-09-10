@extends('layouts.app')

@section('title')
    @include('includes.campaigns-menu')
@endsection

@section('panel-title')
    Add Student
@endsection

@section('panel-content')
    <form class="form-horizontal" role="form" method="POST" action="{!! route('students.index') !!}">
        {{ csrf_field() }}

        <div class="form-group">
            <label for="firstName" class="col-md-2 control-label">First Name</label>
            <div class="col-md-6">
                <input id="firstName" type="text" class="form-control" name="firstName" value="{{ old('firstName') }}" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label for="lastName" class="col-md-2 control-label">Last Name</label>
            <div class="col-md-6">
                <input id="lastName" type="text" class="form-control" name="lastName" value="{{ old('lastName') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email" class="col-md-2 control-label">Email</label>
            <div class="col-md-6">
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" >
            </div>
        </div>

        <div class="form-group">
            <label for="phone" class="col-md-2 control-label">Phone</label>
            <div class="col-md-6">
                <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" >
            </div>
        </div>

        <div class="form-group">
            <label for="address" class="col-md-2 control-label">Address</label>
            <div class="col-md-6">
                <input id="address" type="text" class="form-control" name="address" value="{{ old('address') }}" >
            </div>
        </div>

        <div class="form-group">
            <label for="address" class="col-md-2 control-label">Address 2</label>
            <div class="col-md-6">
                <input id="address2" type="text" class="form-control" name="address2" value="{{ old('address2') }}" >
            </div>
        </div>

        <div class="form-group">
            <label for="city" class="col-md-2 control-label">City</label>
            <div class="col-md-6">
                <input id="city" type="text" class="form-control" name="city" value="{{ old('city') }}" >
            </div>
        </div>

        <div class="form-group">
            <label for="state" class="col-md-2 control-label">State</label>
            <div class="col-md-6">
                <input id="state" type="text" class="form-control" name="state" value="{{ old('state') }}" >
            </div>
        </div>

        <div class="form-group">
            <label for="zip" class="col-md-2 control-label">Zip</label>
            <div class="col-md-6">
                <input id="zip" type="text" class="form-control" name="zip" value="{{ old('zip') }}" >
            </div>
        </div>

        <hr />
        @foreach (session('institution')->fields as $field)
            <div class="form-group">
                <label for="field_{!! $field->id !!}"
                       class="col-md-2 control-label">{!! $field->name !!}</label>
                <div class="col-md-6">
                    <input id="field_{!! $field->id !!}" type="text" class="form-control"
                           name="field_{!! $field->id !!}" value="{{ old('field_'.$field->id) }}">
                </div>
            </div>
        @endforeach

        <hr />

        <div class="form-group">
            <div class="col-md-6 col-md-offset-2">
                <button type="submit" class="btn btn-warning">
                    Create
                </button>
            </div>
        </div>
    </form>
@endsection
