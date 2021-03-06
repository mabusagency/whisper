@extends('layouts.app')

@section('title')
    <h3>Fields</h3>
@endsection

@section('buttons')
    <a href="/fields/create" class="btn btn-warning pull-right btn-sm">Add
        Field</a>
@endsection

@section('panel-content')
    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Merge Tag</th>
            <th>Show in Results</th>
        </tr>
        </thead>
        <tbody>
        @foreach($fields as $field)
            <tr>
                <td><a href="/fields/{!! $field->id !!}">{!! $field->name !!}</a></td>
                <td>*|{!! $field->tag !!}|*</td>
                <td>
                    @if($field->results == 1)
                        yes
                    @else
                        no
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection