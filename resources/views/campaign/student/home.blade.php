@extends('layouts.app')

@section('title')
    @include('includes.campaigns-menu')
@endsection

@section('head')
    <style>
        .share-icon {
            height: 20px;
        }
        .btn-default {
            border-color:#FFC52E;
            color:#FFC52E;
            background-color:#233746;
            padding:12px 12px;
            border-radius:30px;
        }
        .btn-default:hover, .btn-default:active,
        .btn-default:focus, .btn-default.focus {
            background-color:#192833;
            border-color:#FFC52E;
        }
    </style>
@endsection

@section('buttons')

        <a href="#"
           class="btn btn-warning btn-sm btn-filter"
           data-toggle="modal"
           data-target="#filter-students">
            <span class="glyphicon glyphicon-filter"></span> Filter
        </a>
        <span class="table hidden-sm hidden-xs">
            @if((Auth::user()->type == 'admin' || Auth::user()->type == 'institution') && session('institution'))

                <a href="{!! route('students.create') !!}"
                   class="btn btn-warning btn-sm">
                    <span class="glyphicon glyphicon-plus"></span> Add Student
                </a>

                <a href="#"
                   class="btn btn-warning btn-sm"
                   data-toggle="modal"
                   data-target="#upload-students">
                    <span class="glyphicon glyphicon-cloud-upload"></span> Upload
                </a>

                <a href="{!! route('students.export').'?'.$query_string !!}"
                   class="btn btn-warning btn-sm">
                    <span class="glyphicon glyphicon-save"></span> Export</a>
                </a>
            @endif
        </span>

@endsection


@section('panel-content')
    <div class="row">
        <div class="col-md-12">
            <form role="search" action="{!! route('students.search') !!}">
                <div class="form-group">
                    <input type="text" class="form-control" name="query" placeholder="Search">
                </div>
            </form>
        </div>
    </div>

    {{--Desktop--}}
    <table class="table hidden-sm hidden-xs">
        <thead>
        <tr>
            <th class="th-id">#</th>
            <th>Name</th>
            <th>Email</th>
            <th>PURL</th>
            <th class="text-right">Visited</th>
        </tr>
        </thead>
        <tbody>
        @foreach($students as $student)
            <tr>
                <th scope="row">{!! $student->id !!}</th>
                <td>
                    @if($student->trashed())
                        {!! $student->firstName.' '.$student->lastName !!}
                    @else
                        <a href="{!! route('students.update',['id' => $student->id]) !!}">{!! $student->firstName.' '.$student->lastName !!}</a>
                    @endif
                </td>
                <td>{!! $student->email !!}</td>
                <td>
                    @if(!$student->trashed())
                        <a href="{!! \App\Helpers\StudentHelper::get_testing_purl_url($student) !!}" target="_blank">
                            {!! \App\Helpers\StudentHelper::get_purl_url($student) !!}
                        </a>
                    @endif
                </td>
                <td class="text-right">
                    @if($student->trashed())
                        <a href="{!! route('student.restore',[$student->id]) !!}">Restore</a>
                    @else
                        @if(isset($student->results()->first()->created_at))
                            {!! $student->results()->orderBy('id','desc')->first()->created_at->diffForHumans() !!}
                        @endif
                    @endif

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{--Mobile--}}
    <div class="hidden-md hidden-lg">
        @foreach($students as $student)
            <div class="student">
                <div class="row">
                    <div class="col-xs-7">
                        <a href="{!! route('students.update',['id' => $student->id]) !!}" class="student-link">
                            <h4>{!! $student->firstName.' '.$student->lastName !!}</h4>
                        </a>
                    </div>
                    <div class="col-xs- text-right">
                        @if(isset($student->results()->first()->created_at))
                            <p style="font-size:12px;margin-top:10px;">
                                <i>Visited {!! $student->results()->orderBy('id','desc')->first()->created_at->diffForHumans() !!}</i></p>
                        @endif
                    </div>
                </div>
                <div class="row btn-mobile" style="margin-bottom:20px;">
                    <div class="col-xs-3">
                        <a href="tel:{!! $student->phone !!}" class="btn btn-default"><img src="/images/phone.png"
                                                                                           class="share-icon"/></a>
                    </div>
                    <div class="col-xs-3">
                        <a href="sms:{!! $student->phone !!}" class="btn btn-default"><img src="/images/sms.png"
                                                                                           class="share-icon"/></a>
                    </div>
                    <div class="col-xs-3">
                        <a href="https://twitter.com" class="btn btn-default"><img src="/images/twitter.png"
                                                                                   class="share-icon"/></a>
                    </div>
                    <div class="col-xs-3">
                        <a href="https://facebook.com" class="btn btn-default"><img src="/images/facebook.png"
                                                                                    class="share-icon"/></a>
                    </div>
                </div>
            </div>


        @endforeach
    </div>


    <div class="modal fade" id="upload-students" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         style="text-align: left;">
        <form method="POST" action="{{ route('students.upload.preview') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Upload Students</h4>
                    </div>
                    <div class="modal-body">
                        <input type="file" class="form-control" name="csv">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">
                            Upload
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="filter-students" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         style="text-align: left;">
        <form method="GET">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Filter Students</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-3">
                                <label style="margin:0;">Visited</label>
                                @if($pages)
                                    @foreach($pages as $page)
                                        <div class="radio" style="margin-left:-20px;">
                                            <label>
                                                <input type="checkbox" name="page[]" value="{!! $page !!}">
                                                {!! ucfirst($page) !!}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label style="margin:0;">Did Not Visit</label>
                                @if($pages)
                                    @foreach($pages as $page)
                                        <div class="radio" style="margin-left:-20px;">
                                            <label>
                                                <input type="checkbox" name="page[]" value="-{!! $page !!}">
                                                {!! ucfirst($page) !!}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="col-md-3">
                                <label style="margin:0;">Contacted</label>
                                <div class="radio" style="margin-left:-20px;">
                                    <label>
                                        <input type="checkbox" name="contacted" value="Y">
                                        Contacted
                                    </label>
                                </div>
                                <div class="radio" style="margin-left:-20px;">
                                    <label>
                                        <input type="checkbox" name="contacted" value="N">
                                        Not Contacted
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label style="margin:0;">Notes</label>
                                <div class="radio" style="margin-left:-20px;">
                                    <label>
                                        <input type="checkbox" name="notes" value="Y">
                                        Has Notes
                                    </label>
                                </div>
                                <div class="radio" style="margin-left:-20px;">
                                    <label>
                                        <input type="checkbox" name="notes" value="N">
                                        No Notes
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <hr/>

                                <div class="radio" style="margin-left:-20px;">
                                    <label>
                                        <input type="checkbox" name="deleted" value="Y">
                                        Show Deleted
                                    </label>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
