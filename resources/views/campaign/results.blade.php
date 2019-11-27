@extends('layouts.app')

@section('title')
    @include('includes.campaigns-menu')
@endsection

@section('head')
    <style>
        .panel {
            margin-bottom:50px;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="page-title"><span>Academics</span></div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <div style="float:right;"><img src="/images/purls_sent.png" width="30"/></div>
                    PURLs sent
                </div>
            </div>
            <div class="panel-body">
                <div class="number">100%</div>
                <p class="panel-title">{!! $students !!} PURLs</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <div style="float:right;"><img src="/images/purls_opened.png" width="30"/></div>
                    PURLs opened
                </div>
            </div>
            <div class="panel-body">
                <div class="number">{!! round($visits/$students*100,0) !!}%</div>
                <p class="panel-title">{!! $visits !!} PURLs</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <div style="float:right;"><img src="/images/purls_completed.png" width="30"/></div>
                    PURLs completed
                </div>
            </div>
            <div class="panel-body">
                <div class="number">{!! round($completed/$students*100,0) !!}%</div>
                <p class="panel-title">{!! $completed !!} PURLs</p>
            </div>
        </div>
    </div>
</div>

{{--<div class="panel panel-default">--}}
    {{--<div class="panel-heading">--}}
        {{--<h3 class="panel-title">Visits</h3>--}}
    {{--</div>--}}
    {{--<div class="panel-body">--}}
        {{--<div id="chart-visits" style="height: 220px;"></div>--}}

        {{--<table class="table">--}}
            {{--<thead>--}}
            {{--<tr>--}}
                {{--<th>Page</th>--}}
                {{--<th>Visit Count</th>--}}
                {{--<th></th>--}}
            {{--</tr>--}}
            {{--</thead>--}}
            {{--<tbody>--}}
            {{--@foreach($visits_total as $visit)--}}
                {{--<tr>--}}
                    {{--<td>{!! $visit->page !!}</td>--}}
                    {{--<td><a href="/campaign/students?page[]={!! $visit->page !!}">{!! $visit->num !!}</a></td>--}}
                    {{--<td></td>--}}
                {{--</tr>--}}
            {{--@endforeach--}}
            {{--</tbody>--}}
        {{--</table>--}}
    {{--</div>--}}
{{--</div>--}}

{{--<div class="panel panel-default">--}}
    {{--<div class="panel-heading">--}}
        {{--<h3 class="panel-title">Links</h3>--}}
    {{--</div>--}}
    {{--<div class="panel-body">--}}
        {{--<div class="row">--}}
            {{--<div class="col-md-6">--}}
                {{--<table class="table">--}}
                    {{--<thead>--}}
                    {{--<tr>--}}
                        {{--<th>Link</th>--}}
                        {{--<th>Click Count</th>--}}
                        {{--<th></th>--}}
                    {{--</tr>--}}
                    {{--</thead>--}}
                    {{--<tbody>--}}
                    {{--@foreach($links as $link)--}}
                        {{--<tr>--}}
                            {{--<td>{!! str_limit($link->url,20) !!}</td>--}}
                            {{--<td>{!! $link->num !!}</td>--}}
                            {{--<td></td>--}}
                        {{--</tr>--}}
                    {{--@endforeach--}}
                    {{--</tbody>--}}
                {{--</table>--}}
            {{--</div>--}}
            {{--<div class="col-md-6">--}}
                {{--<div id="chart-links" style="margin-top:14px; height: 150px;"></div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}

@foreach($fields as $field)
    @if(count($polls[$field->tag]) > 0)
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1" style="margin-top:30px;">
                        <h3 class="panel-title" style="font-size:20px;font-weight:600;">{!! $field->name !!}</h3>
                        <div style="font-weight:800;">
                            <div class="dropdown panel-title" style="font-size:14px;margin-top:10px;">
                                Sort by
                                <a class="dropdown-toggle" type="button" id="dropdownMenu{!! $field->id !!}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    Highest to lowest
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a href="#" class="sort" dir="desc" field="{!! $field->id !!}">Highest to lowest</a></li>
                                    <li><a href="#" class="sort" dir="asc" field="{!! $field->id !!}">Lowest to highest</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        @if(count($polls[$field->tag]) > 5) <div class="table-results" id="table-results-{!! $field->id !!}"> @endif
                            <table class="table" id="{!! $field->id !!}">
                                <tbody>
                                @foreach($polls[$field->tag] as $poll)
                                <tr style="font-size:20px;">
                                    <td class="poll-value">{!! $poll['value'] !!}</td>
                                    <td class="text-right" style="color:#b7b7b7;font-weight:800;" num="{!! $poll['num'] !!}">
                                        {!! $poll['num'] !!}
                                        @if($completed > 0)
                                            {!! $poll['num'] !!} ({!! round($poll['num']/$completed*100,0) !!}%)
                                        @endif

                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @if(count($polls[$field->tag]) > 5)
                            <p class="show-more panel-title" field="{!! $field->id !!}" style="font-size:14px;"><a href="#" class="button">Show More</a></p>
                        </div>
                        @endif
                    </div>
                    {{--<div class="col-md-6">--}}
                        {{--<div id="chart-{!! $field->tag !!}" style="height: 200px;"></div>--}}
                    {{--</div>--}}
                </div>
            </div>
        </div>
    @endif
@endforeach





@endsection


@section('foot')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="/js/highcharts-theme.js"></script>

    <script>
        $(function() {
            $( ".sort" ).click(function() {
                var field = $(this).attr('field');
                var sort = $(this).attr('dir');
                sortTable( $('#'+field), sort );
                $('#dropdownMenu'+field).text($(this).text());
                return false;
            });
            $( ".show-more" ).click(function() {
                var field = $(this).attr('field');

                $('#table-results-'+field).css('max-height','inherit');
                $(this).hide();
                return false;
            });
        });

        function sortTable(table, order) {
            var asc   = order === 'asc',
                tbody = table.find('tbody');

            tbody.find('tr').sort(function(a, b) {
                if (asc) {
                    return parseInt($('td:nth-child(2)', a).attr('num')) - parseInt($('td:nth-child(2)', b).attr('num'));
                } else {
                    return parseInt($('td:nth-child(2)', b).attr('num')) - parseInt($('td:nth-child(2)', a).attr('num'));
                }
            }).appendTo(tbody);
        }
    </script>

    <script>
        Highcharts.chart('chart-visits', {
            chart: {
                height: 200
            },
            series: [
                <?php foreach($page_visits_for_chart as $page => $visits) { ?>
                {
                    name: '<?= $page; ?>',
                    data: [
                            <?php
                            foreach($days_for_chart as $day) {
                                if(isset($visits[$day])) {
                                    echo $visits[$day].',';
                                } else {
                                    echo '0,';
                                }
                            }
                            ?>
                    ]
                },
                <?php } ?>


            ],
            xAxis: {
                categories: [
                    @foreach($days_for_chart as $day)
                        '{!! $day !!}',
                    @endforeach
                ],
            },

        });
        Highcharts.chart('chart-links', {
            chart: {
                height: <?= count($links)*42; ?>,
            },
            plotOptions: {
                series: {
                    pointWidth: 15,
                }
            },
            xAxis: {
                categories: [
                    @foreach($links as $link)
                        '{!! str_limit($link->url,20) !!}',
                    @endforeach
                ],
                crosshair: true
            },
            series: [{
                type: 'bar',
                name: 'clicks',
                data: [
                    @foreach($links as $link)
                        {!! $link->num !!},
                    @endforeach
                ]
            }]
        });
        @foreach($fields as $field)
        Highcharts.chart('chart-{!! $field->tag !!}', {
            chart: {
                height: 200,
                type: 'pie'
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        enabled: false,
                    }
                }
            },
            series: [{
                name: '{!! $field->name !!}',
                colorByPoint: true,
                data: [
                @foreach($polls[$field->tag] as $poll)
                    {
                        name: '{!! $poll['value'] !!}',
                        y: {!! $poll['num'] !!}
                    },
                    @endforeach
                    ]
            }]
        });
        @endforeach
    </script>
@endsection
