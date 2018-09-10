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

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Visits</h3>
        </div>
        <div class="panel-body">
            <div id="chart-visits" style="height: 220px;"></div>

            <table class="table">
                <thead>
                <tr>
                    <th>Page</th>
                    <th>Visit Count</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($visits_total as $visit)
                    <tr>
                        <td>{!! $visit->page !!}</td>
                        <td><a href="/campaign/students?page[]={!! $visit->page !!}">{!! $visit->num !!}</a></td>
                        <td></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Links</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Link</th>
                            <th>Click Count</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($links as $link)
                            <tr>
                                <td>{!! str_limit($link->url,20) !!}</td>
                                <td>{!! $link->num !!}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <div id="chart-links" style="margin-top:14px; height: 150px;"></div>
                </div>
            </div>
        </div>
    </div>

    @foreach($fields as $field)
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{!! $field->name !!}</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{!! $field->name !!}</th>
                            <th>Students</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($polls[$field->tag] as $poll)
                        <tr>
                            <td>{!! $poll['value'] !!}</td>
                            <td>{!! $poll['num'] !!}</td>
                            <td></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <div id="chart-{!! $field->tag !!}" style="height: 200px;"></div>
                </div>
            </div>
        </div>
    </div>
    @endforeach





@endsection


@section('foot')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="/js/highcharts-theme.js"></script>

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
