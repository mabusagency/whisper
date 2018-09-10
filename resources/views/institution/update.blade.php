@extends('layouts.app')

@section('panel-title')
   Update Institution
@endsection

@section('panel-content')
    <form class="form-horizontal" role="form" method="POST" action="{!! route('institutions.update', ['id' => $institution->id]) !!}" enctype="multipart/form-data">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <div class="form-group">
            <label for="name" class="col-md-2 control-label">Institution Name</label>

            <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $institution->name) }}" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label for="contact" class="col-md-2 control-label">Contact Name</label>

            <div class="col-md-6">
                <input id="contact" type="text" class="form-control" name="contact" value="{{ old('contact', $institution->contact) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email" class="col-md-2 control-label">E-Mail Address</label>

            <div class="col-md-6">
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email', $institution->user->email) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="address" class="col-md-2 control-label">Address</label>

            <div class="col-md-6">
                <input id="address" type="text" class="form-control" name="address" value="{{ old('address', $institution->address) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="city" class="col-md-2 control-label">City</label>

            <div class="col-md-6">
                <input id="city" type="text" class="form-control" name="city" value="{{ old('city', $institution->city) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="state" class="col-md-2 control-label">State</label>

            <div class="col-md-6">
                <input id="state" type="text" class="form-control" name="state" value="{{ old('state', $institution->state) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="zip" class="col-md-2 control-label">Zip</label>

            <div class="col-md-6">
                <input id="zip" type="text" class="form-control" name="zip" value="{{ old('zip', $institution->zip) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="col-md-2 control-label">Update Password</label>

            <div class="col-md-6">
                <input id="password" type="password" class="form-control" name="password">
            </div>
        </div>

        <hr />

        <div class="form-group">
            <label for="mailchimp_key" class="col-md-2 control-label">MailChimp Key</label>

            <div class="col-md-6">
                <input type="text" id="mailchimp_key" class="form-control" name="mailchimp_key" value="{{ old('mailchimp_key', $institution->mailchimp_key) }}">
            </div>
        </div>

        <hr />

        <div class="form-group">
            <label for="logo" class="col-md-2 control-label">Logo (optional)</label>

            <div class="col-md-6">
                @if($institution->logo)
                    <img src="/uploads/logo/{!! $institution->logo !!}"  style="height:80px;margin-bottom:20px;"/>
                @endif
                <input type="file" id="logo" class="form-control" name="logo">
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-6 col-md-offset-2">
                <button type="submit" class="btn btn-warning">
                    Update
                </button>
            </div>
        </div>
    </form>

    <hr/>

    <div class="col-md-12 text-right">
        <a class="btn btn-danger" data-toggle="modal" data-target="#{!! $institution->id !!}">
            Delete Institution
        </a>
        @include('includes/delete-modal', ['id' => $institution->id, 'object' => 'Institution', 'name' => $institution->name, 'uri' => "/institutions/$institution->id"])
    </div>
@endsection
