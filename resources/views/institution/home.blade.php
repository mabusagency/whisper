@extends('layouts.app')

@section('title')
    <h3>Institutions</h3>
@endsection

@section('buttons')
    <a href="/institutions/create" class="btn btn-warning pull-right btn-sm crud-buttons">Add
        Institution</a>
@endsection

@section('panel-content')

    <table class="table">
        <thead>
        <tr>
            <th style="width:50px;">#</th>
            <th>Name</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($institutions as $institution)
            <tr>
                <th scope="row">{!! $institution->id !!}</th>
                <td>
                    <a href="/institutions/set/{!! $institution->id !!}">{!! $institution->name !!}</a>
                </td>
                <td class="text-right">
                    <a href="{!! route('institutions.show',['id' => $institution->id]) !!}">Settings</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection
