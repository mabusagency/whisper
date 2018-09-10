@extends('layouts.app')

@section('content')
    <div class="col-md-4">
        @include('includes.settings-menu')
    </div>

    <!-- Tab Panels -->
    <div class="col-md-8">
        <div class="tab-content">
            <!-- Profile -->
            <div role="tabpanel" class="tab-pane active" id="profile">

                <div class="panel panel-default">
                    <div class="panel-heading">Update Password</div>

                    <div class="panel-body">
                        <!-- Success Message -->
                        {{--<div class="alert alert-success" v-if="form.successful">--}}
                        {{--Your password has been updated!--}}
                        {{--</div>--}}

                        <form class="form-horizontal" role="form" method="post" action="{!! route('users.update.security') !!}">

                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            <!-- Current Password -->
                            <div class="form-group">
                                <label class="col-md-4 control-label">Current Password</label>

                                <div class="col-md-6">
                                    <input type="password" class="form-control" name="current_password">
                                </div>
                            </div>

                            <!-- New Password -->
                            <div class="form-group">
                                <label class="col-md-4 control-label">Password</label>

                                <div class="col-md-6">
                                    <input type="password" class="form-control" name="password">
                                </div>
                            </div>

                            <!-- New Password Confirmation -->
                            <div class="form-group">
                                <label class="col-md-4 control-label">Confirm Password</label>

                                <div class="col-md-6">
                                    <input type="password" class="form-control" name="password_confirmation">
                                </div>
                            </div>

                            <!-- Update Button -->
                            <div class="form-group">
                                <div class="col-md-offset-4 col-md-6">
                                    <button type="submit" class="btn btn-warning">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>


@endsection
