@extends('layouts.app')

@section('panel-title')
    Upload Students
@endsection

@section('panel-content')
    <div class="row">
        <form method="post" action="{!! route('students.upload.execute') !!}">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Field</th>
                        <th>
                            <div class="pull-right smaller" style="font-weight:normal;">
                                Review another: <a href="javascript:void(0)" id="previous-data">Previous</a> | <a
                                        href="javascript:void(0)" id="next-data">Next</a>
                            </div>
                            CSV Data
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($preview_data[0] as $key => $column)
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-xs-10">
                                        <select class="form-control" v-model="field" @change="fieldSelected" id=
                                        "{!! $preview_data[0][$key] !!}" name="columns[{!! $key !!}]">
                                        {{--<option value=""></option>--}}
                                        <option value="firstName">
                                            First Name
                                        </option>
                                        <option value="lastName">
                                            Last Name
                                        </option>
                                        <option value="company">
                                            Company
                                        </option>
                                        <option value="email">
                                            Email
                                        </option>
                                        <option value="phone">
                                            Phone
                                        </option>
                                        <option value="address">
                                            Address
                                        </option>
                                        <option value="address2">
                                            Address2
                                        </option>
                                        <option value="city">
                                            City
                                        </option>
                                        <option value="state">
                                            State
                                        </option>
                                        <option value="zip">
                                            Zip
                                        </option>
                                        @foreach(\App\Field::where('institution_id',session('institution')->id)->get() as $field)
                                            <option value="{!! strtolower($field->tag) !!}">
                                                {!! str_limit($field->name,20) !!}
                                            </option>
                                        @endforeach
                                        <option value="new-field">
                                            << new field >>
                                        </option>
                                        </select>
                                    </div>
                                </div>

                            </td>

                            @foreach($preview_data as $i => $data)
                                <td class="data-cell" data-key="{{ $i }}" style="{{ $i != 0 ? 'display: none' : '' }}">
                                    {!! str_limit($data[$key],20) !!}
                                </td>
                            @endforeach

                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <hr/>

                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input type="hidden" name="csv_file" value="{!! $csv_file !!}">
                <button type="submit" class="btn btn-warning">Upload</button>

            </div>
        </form>
    </div>

@stop

@section('foot')
    <script>
        function auto_populate_from_user_data() {
            var used_fields = [];
            $("select").each(function () {
                var columnHeader = this.id.toLowerCase().trim();
                console.log(columnHeader);

                if ($.inArray(columnHeader, used_fields) == -1) {
                    if (columnHeader.indexOf('first') > -1 && columnHeader.indexOf('name') > -1) {
                        $(this).parent().next('.check').show();
                        $(this).val('firstName');
                    }
                    else if (columnHeader.indexOf('last') > -1 && columnHeader.indexOf('name') > -1) {
                        $(this).val('lastName');
                    }
                    else {
                        $(this).val(columnHeader);
                    }

                    used_fields.push(columnHeader);
                }
            });
        }

        function disable_options() {
            $("option").prop('disabled', false);
            $("select").each(function () {
                var optionSelected = $("option:selected", this);
                var valueSelected = this.value;
                $("option[value='" + valueSelected + "']").not(optionSelected).not("option[value='new-field']").prop('disabled', true);
            });
        }

        function show_new_field() {
            if (this.value == 'new-field') {
                $(this).hide();
                $(this).after('<input type="text" class="form-control new-field" placeholder="Name of the new field" name=' + this.name + ' />');
            } else if ($(this).next().length) {
                $(this).next().remove();
            }
        }

        function enable_next_prev_links() {
            var current_value = 0;
            var max_value = Number('{{ count($preview_data) - 1 }}');

            $('#next-data').click(function () {
                if (current_value != max_value) {
                    current_value += 1;
                } else {
                    current_value = 0;
                }
                $('td.data-cell').hide();
                $('td.data-cell[data-key=' + current_value + ']').show();
            });

            $('#previous-data').click(function () {
                if (current_value != 0) {
                    current_value -= 1;
                } else {
                    current_value = max_value;
                }
                $('td.data-cell').hide();
                $('td.data-cell[data-key=' + current_value + ']').show();
            });
        }

        $(function () {
            auto_populate_from_user_data();

            disable_options();

            enable_next_prev_links();

            $('select').on('change', function () {
                disable_options();
                show_new_field.apply(this);
            });

        });
    </script>

@stop