@extends('layouts.app')

@section('panel-title')
    Update Field
@endsection

@section('panel-content')

    <form class="form-horizontal" role="form" method="POST" action="{!! route('fields.update',['id' => $field->id]) !!}">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <div class="form-group">
            <label for="name" class="col-md-2 control-label">Name</label>
            <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name',$field->name) }}"
                       required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label for="tag" class="col-md-2 control-label">Merge Tag</label>
            <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="tag" value="{{ old('tag',$field->tag) }}"
                       required autofocus>
            </div>
        </div>

        <div class="form-group form-check">
            <div class="col-md-6 col-md-offset-2">
                <input type="checkbox" class="form-check-input" id="results" name="results" @if($field->results) checked @endif value="1">
                &nbsp;<label class="form-check-label" for="results" >Show in Results</label>
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
        <a class="btn btn-danger" data-toggle="modal" data-target="#{!! $field->id !!}" >
            Delete Field
        </a>
        @include('includes/delete-modal', ['id' => $field->id, 'object' => 'Field', 'name' => $field->name, 'uri' => "/fields/$field->id", 'mailchimp' => true])
    </div>


@endsection
