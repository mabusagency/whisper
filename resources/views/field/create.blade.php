@extends('layouts.app')

@section('panel-title')
    Add Field
@endsection

@section('panel-content')

    <form class="form-horizontal" role="form" method="POST" action="/fields">
        {{ csrf_field() }}

        <div class="form-group">
            <label for="name" class="col-md-2 control-label">Name</label>

            <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required
                       autofocus>
            </div>
        </div>

        <div class="form-group">
            <label for="tag" class="col-md-2 control-label">Merge Tag</label>
            <div class="col-md-6">
                <input id="tag" type="text" class="form-control" name="tag" value="{{ old('tag') }}"
                       required autofocus>
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

@section('foot')
    <script>
        $(function() {
            $( "#name" ).keyup(function() {
                $('#tag').val($( "#name" ).val().toUpperCase());
            });
        });
    </script>
@endsection