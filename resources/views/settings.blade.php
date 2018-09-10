@extends('layouts.app')

@section('content')
        <!-- Tabs -->
        <div class="col-md-4">
            @include('includes.settings-menu')
        </div>

        <!-- Tab Panels -->
        <div class="col-md-8">
            <div class="tab-content">
                <!-- Profile -->
                <div role="tabpanel" class="tab-pane active" id="profile">
                    <div class="panel panel-default">
                        <div class="panel-heading">Profile</div>

                        <div class="panel-body">

                            <form class="form-horizontal" role="form" method="post" action="{!! route('users.update') !!}">

                                {{ csrf_field() }}
                                {{ method_field('PUT') }}

                                <!-- E-Mail Address -->
                                <div class="form-group">
                                    <label class="col-md-4 control-label">E-Mail Address</label>

                                    <div class="col-md-6">
                                        <input type="email" class="form-control" name="email"
                                               value="{{ \Auth::user()->email }}">
                                    </div>
                                </div>

                                <!-- Update Button -->
                                <div class="form-group">
                                    <div class="col-md-offset-4 col-md-6">
                                        <button type="submit" class="btn btn-warning">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
