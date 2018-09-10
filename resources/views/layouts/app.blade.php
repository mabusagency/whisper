<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/recruitu.css') }}" rel="stylesheet">
    @if(Auth::user() && Auth::user()->type == 'staff')
        <style>
            .navbar {
                margin-bottom: 20px;
            }
            @media only screen and (max-width: 991px) {
                body {
                    background-color: #192833;
                    color:white;
                }
                .content-header h3 {
                    color:white;
                }
                .panel-default, .panel-default > .panel-heading {
                    background-color: #192833;
                    border-color:#192833;
                }
                .panel-body {
                    padding:0;
                }
                .student {
                    background-color:#233746;
                    margin-bottom:10px;
                    margin-left:-20px;
                    margin-right:-15px;
                    padding:10px 35px;
                }
                .form-control,   .form-control::placeholder {
                    background-color:#233746;
                    border-color:#233746;
                    color:#3fbeac;
                }
                .student-link {
                    color:white;
                }
                .campaign-back-link a {
                    color:white;
                }
                a {
                    color:white;
                }
                .content-header {
                    margin-bottom:10px;
                }
                .panel-primary {
                    margin-top:20px;
                }
                .btn-filter {
                    background-color:#192833;
                    border-color:#8e3741;
                    color:white;
                }
                .modal-content {
                    background-color: #192833;
                }
                .modal-content hr {
                    border-color: #394046;
                }
                .modal-header {
                    border-bottom-color: #394046;
                }
                .modal-footer {
                    border-top-color: #394046;
                }
                .close {
                    color:#e4d4d4;
                }
            }
        </style>
    @endif
    @yield('head')
</head>
<body>
<div id="app">
    <nav class="navbar navbar-default navbar-static-top navbar-inverse">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/home') }}">
                    @if(Auth::user() && Auth::user()->type != 'admin' && session('institution') && session('institution')->logo != '')
                        <img src="/uploads/logo/{!! session('institution')->logo !!}"
                             style="margin-top:-13px;max-height:50px;"/>
                    @else
                        <div class="hidden-md hidden-lg">
                            <img src="/images/w.png" style="max-height:15px;"/>
                        </div>
                        <div class="table hidden-sm hidden-xs">
                            <img src="/images/whisper_logo.png" style="max-height:22px;"/>
                        </div>
                    @endif
                </a>

                @if(session('institution') && isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '/institutions')
                    <a class="navbar-brand" href="{{ url('/campaigns') }}" style="font-size:13px;font-weight:500;">
                            {!! session('institution')->name !!}
                    </a>
               @endif

            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ route('login') }}">Login</a></li>
                        {{--<li><a href="{{ route('register') }}">Register</a></li>--}}
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                {{ Auth::user()->email }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">

                                <li>
                                    <a href="/settings/profile">Settings</a>
                                </li>

                                <li>
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @if (Auth::user() && Auth::user()->type == 'admin' && session('institution') && $_SERVER['REQUEST_URI'] != '/institutions')
        <div class="navbar-inverse campaign-nav">
            <div class="container">
                <ul class="nav nav-tabs" style="margin-bottom:-1px">
                    <li role="presentation" class="@if(strstr(Request::path(),'campaign')) active @endif"><a
                                href="/campaigns">Campaigns</a></li>
                    <li role="presentation" class="@if(strstr(Request::path(),'staff')) active @endif"><a
                                href="/staff">Staff</a></li>
                    <li role="presentation" class="@if(strstr(Request::path(),'field')) active @endif"><a
                                href="/fields">Fields</a></li>

                </ul>
            </div>
        </div>

    @elseif (Auth::user() && (Auth::user()->type == 'institution'))
        <div style="background-color:#86364E;margin-top:-50px;padding-top:20px;">
            <div class="container">
                <ul class="nav nav-tabs">
                    <li role="presentation" class="@if(strstr(Request::path(),'campaign')) active @endif"><a
                                href="/campaigns">Campaigns</a></li>
                    <li role="presentation" class="@if(strstr(Request::path(),'staff')) active @endif"><a
                                href="/staff">Staff</a></li>
                </ul>
            </div>
        </div>

    @endif

    <div class="container">
        <div class="row">

            <div class="col-md-12">

                @if(\Request::input('error') || ( isset($error) && ( $error != '0' && count($error) > 0) )  )

                    <?php if (\Request::input('error')) $error = \Request::input('error'); ?>

                    @if(is_array($error))
                        @foreach($error as $row)
                            <div class="alert alert-danger">{!! $row !!}</div>
                        @endforeach
                    @else
                        <div class="alert alert-danger">{!! $error !!}</div>
                    @endif

                @elseif (\Request::input('message') || (isset($message) && $message != '0' && $message != ''))

                    <?php if (\Request::input('message')) $message = \Request::input('message'); ?>

                    <div class="alert alert-success">{!! $message !!}</div>

                @elseif (\Request::input('info') || (isset($info) && $info != '0' && $info != ''))

                    <?php if (\Request::input('info')) $info = \Request::input('info'); ?>

                    <div class="alert alert-warning">{!! $info !!}</div>

                @elseif (\Request::input('mailchimp_error'))

                    <div class="alert alert-danger"><img src="/images/freddie.png"
                                                         height="30"/>&nbsp;&nbsp;&nbsp;{!! \Request::input('mailchimp_error') !!}
                    </div>

                @elseif (count($errors) > 0)
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach


                @elseif(Session::has('message'))
                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>

                @endif

                @if(View::hasSection('title'))
                    <div class="content-header">
                        @if(View::hasSection('buttons'))
                            <div class="pull-right">@yield('buttons')</div>
                        @endif

                        @yield('title')
                    </div>
                @endif

                @yield('content')

                @if(View::hasSection('panel-content'))
                    <div class="panel panel-default">
                        @if(View::hasSection('panel-title'))
                            <div class="panel-heading">
                                @yield('panel-title')
                            </div>
                        @endif

                        <div class="panel-body">
                            @yield('panel-content')
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mailchimp-campaign-help" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     style="text-align: left;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">MailChimp Integration</h4>
            </div>
            <div class="modal-body">
                <img src="/images/mailchimp_campaign_directions.png" style="width:100%"/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>

<!-- Tend Code -->
<!-- Tend Code -->
<script type="text/javascript">
    var tendKey = "VRmYYWRNb5SavT2q2ywy";
</script>
<script type="text/javascript">
    !function () {
        function t() {
            var t = n.createElement("script");
            t.type = "text/javascript", t.async = !0, t.src = "http://tend51:8888/js/track/v3.3.js";
            var e = n.getElementsByTagName("script")[0];
            e.parentNode.insertBefore(t, e)
        }

        var e = window;
        if (!window.tend) {
            var n = document, a = function () {
                return {
                    event: function (t) {
                        a.c(["event", t])
                    }, page: function (t) {
                        a.c(["page", t])
                    }
                }
            }();
            a.q = [], a.c = function (t) {
                a.q.push(t)
            }, e.tend = a, e.attachEvent ? e.attachEvent("onload", t) : e.addEventListener("load", t, !1)
        }
    }();
</script>


@yield('foot')
</body>
</html>
