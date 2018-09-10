@extends('layouts.app')

@section('title')
    @include('includes.campaigns-menu')
@endsection

@section('panel-title')
    Campaigns Settings
@endsection

@section('panel-content')
    <form class="form-horizontal" role="form" method="POST" action="{!! route('campaigns.update', ['id' => session('campaign')->id]) !!}">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <div class="form-group">
            <label for="name" class="col-md-2 control-label">Name</label>
            <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name" value="{{ session('campaign')->name }}" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label for="domain" class="col-md-2 control-label">Domain</label>
            <div class="col-md-6">
                <input id="domain" type="text" class="form-control" name="domain" value="{{ session('campaign')->domain }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="ftp_username" class="col-md-2 control-label">FTP User</label>
            <div class="col-md-6">
                <input id="ftp_username" type="text" class="form-control" name="ftp_username" value="{{ session('campaign')->ftp_username }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="ftp_path" class="col-md-2 control-label">FTP Path</label>
            <div class="col-md-6">
                <input id="ftp_path" type="text" class="form-control" name="ftp_path" value="{{ session('campaign')->ftp_path }}" required>
            </div>
        </div>

        <hr />

        <div class="form-group">
            <label for="ftp_path" class="col-md-2 control-label">Last Page</label>
            <div class="col-md-6">
                <input id="ftp_path" type="text" class="form-control" name="last_page" value="{{ session('campaign')->last_page }}" placeholder="/thank-you">
            </div>
        </div>

        <div class="form-group">
            <label for="ftp_path" class="col-md-2 control-label">Redirect Page</label>
            <div class="col-md-6">
                <input id="ftp_path" type="text" class="form-control" name="redirect_page" value="{{ session('campaign')->redirect_page }}" placeholder="/welcome-back">
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

    <hr />

    <div class="col-md-12 text-right">
        <a class="btn btn-default" data-toggle="modal" data-target="#contacts_{!! session('campaign')->id !!}" >
            Delete All Contacts & Results
        </a>
        @include('includes/delete-modal', ['id' => 'contacts_'.session('campaign')->id, 'object' => 'Campaign\'s Contacts and Results', 'name' => 'contacts and results for '.session('campaign')->name, 'uri' => "/campaigns/".session('campaign')->id."/contacts"])

        <a class="btn btn-danger" data-toggle="modal" data-target="#{!! session('campaign')->id !!}" >
            Delete Campaign
        </a>
        @include('includes/delete-modal', ['id' => session('campaign')->id, 'object' => 'Campaign', 'name' => session('campaign')->name, 'uri' => "/campaigns/".session('campaign')->id])

    </div>

@endsection
