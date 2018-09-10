@extends('layouts.app')

@section('panel-title')
   Add Institution
@endsection

@section('panel-content')
    <form class="form-horizontal" role="form" method="POST" action="/institutions" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="form-group">
            <label for="name" class="col-md-2 control-label">Institution Name</label>

            <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label for="contact" class="col-md-2 control-label">Contact Name</label>

            <div class="col-md-6">
                <input id="contact" type="text" class="form-control" name="contact" value="{{ old('contact') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email" class="col-md-2 control-label">E-Mail Address</label>

            <div class="col-md-6">
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="address" class="col-md-2 control-label">Address</label>

            <div class="col-md-6">
                <input id="address" type="text" class="form-control" name="address" value="{{ old('address') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="city" class="col-md-2 control-label">City</label>

            <div class="col-md-6">
                <input id="city" type="text" class="form-control" name="city" value="{{ old('city') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="state" class="col-md-2 control-label">State</label>

            <div class="col-md-6">
                <input id="state" type="text" class="form-control" name="state" value="{{ old('state') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="zip" class="col-md-2 control-label">Zip</label>

            <div class="col-md-6">
                <input id="zip" type="text" class="form-control" name="zip" value="{{ old('zip') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="col-md-2 control-label">Password</label>

            <div class="col-md-6">
                <input id="password" type="password" class="form-control" name="password" required>
            </div>
        </div>

        <div class="form-group">
            <label for="password-confirm" class="col-md-2 control-label">Confirm Password</label>

            <div class="col-md-6">
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
            </div>
        </div>

        <hr />

        <div class="form-group">
            <label for="mailchimp_key" class="col-md-2 control-label">MailChimp Key</label>

            <div class="col-md-6">
                <input type="text" id="mailchimp_key" class="form-control" name="mailchimp_key">
            </div>
        </div>

        <hr />

        <div class="form-group">
            <label for="logo" class="col-md-2 control-label">Logo (optional)</label>

            <div class="col-md-6">
                <input type="file" id="logo" class="form-control" name="logo">
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-6 col-md-offset-2">
                <button type="submit" class="btn btn-warning">
                    Create
                </button>
            </div>
        </div>
    </form>
@endsection
