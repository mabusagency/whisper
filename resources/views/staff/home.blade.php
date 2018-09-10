@extends('layouts.app')

@section('title')
    <h3>Staff</h3>
@endsection


@if(Auth::user()->type == 'admin' && session('institution'))
@section('buttons')
    <a href="/staff/create"
       class="btn btn-warning pull-right btn-sm">Add Staff</a>
@endsection
@endif


@section('panel-content')
    <table class="table">
        <thead>
        <tr>
            <th style="width:50px;">#</th>
            <th>Name</th>
            <th>Role</th>
            @if(Auth::user()->type == 'admin')
                <th>Students</th>
            @endif
            <th>Recruits</th>
        </tr>
        </thead>
        <tbody>
        @foreach($staff as $staff)
            <tr>
                <th scope="row">{!! $staff->id !!}</th>
                <td>
                    <a href="/staff/{!! $staff->id !!}">{!! $staff->name !!}</a>
                </td>
                <td>
                    {!! $staff->role !!}
                </td>
                @if(Auth::user()->type == 'admin')
                    <td>
                        {!! $staff->students->count() !!}
                    </td>
                @endif
                <td>
                    {!! $staff->recruits->count() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>


@endsection