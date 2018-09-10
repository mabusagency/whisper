@extends('layouts.app')

@section('panel-title')
    Add Campaign
@endsection

@section('content')
    <div class="alert alert-warning">Let's start by creating a campaign.</div>
@endsection

@section('panel-content')

    <form class="form-horizontal" role="form" method="POST" action="{!! route('campaigns.store') !!}">
        {{ csrf_field() }}

        <div class="form-group">
            <label for="name" class="col-md-2 control-label">Name *</label>

            <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required
                       autofocus>
            </div>
        </div>

        <hr/>

        
        <div class="form-group">
            <div class="col-md-6 col-md-offset-2">
                <div class="alert alert-warning" >For testing purposes, you can leave the below fields blank. This will install the campaign on a temporary domain. </div>
            </div>
        </div>

        <div class="form-group">
            <label for="domain" class="col-md-2 control-label">Domain</label>

            <div class="col-md-6">
                <input id="domain" type="text" class="form-control" name="domain" value="{{ old('domain') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="ftp_server" class="col-md-2 control-label">FTP Server</label>

            <div class="col-md-6">
                <input id="ftp_server" type="text" class="form-control" name="ftp_server"
                       value="{{ old('ftp_server') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="ftp_username" class="col-md-2 control-label">FTP Username</label>

            <div class="col-md-6">
                <input id="ftp_username" type="text" class="form-control" name="ftp_username"
                       value="{{ old('ftp_username') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="ftp_password" class="col-md-2 control-label">FTP Password</label>

            <div class="col-md-6">
                <input id="ftp_password" type="password" class="form-control" name="ftp_password"
                       value="{{ old('ftp_password') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="ftp_path" class="col-md-2 control-label">FTP Path</label>

            <div class="col-md-6">
                <input id="ftp_path" type="text" class="form-control" name="ftp_path" value="{{ old('ftp_path') }}">
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
