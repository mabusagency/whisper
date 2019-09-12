@extends('layouts.app')

@section('title')
    <h3>Campaigns</h3>
@endsection

@if(Auth::user()->type == 'admin' && session('institution'))
@section('buttons')
    <a href="/campaigns/create"
       class="btn btn-warning pull-right btn-sm">Add Campaign</a>
@endsection
@endif

@section('panel-content')

    <table class="table">
        <thead>
        <tr>
            <th class="th-id" style="width:50px;">#</th>
            <th>Name</th>
            @if(Auth::user()->type == 'admin')
                <th>Students</th>
            @endif
            <th>Recruits</th>
            {{--<th>Status</th>--}}
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($campaigns as $campaign)
            <tr>
                <th scope="row">{!! $campaign->id !!}</th>
                <td style="width:300px;">
                    <a href="/campaigns/set/{!! $campaign->id !!}">{{ $campaign->name }}</a>
                </td>
                @if(Auth::user()->type == 'admin')
                    <td>{!! $campaign->students->count() !!}</td>
                @endif
                @if(Auth::user()->type != 'staff')
                    <td>{!! $campaign->recruits->count() !!}</td>
                @else
                    <td>{!!
                        \App\Student::where('converted',1)
                        ->where('campaign_id',$campaign->id)
                        ->whereHas('staff', function ($query) {
                            $query->where('id', Auth::user()->staff->id);
                        })->count()
                        !!}
                    </td>
                @endif
                {{--<td>--}}
                {{--@if($status[$campaign->id])--}}
                {{--<span style="color:red">{!! $status[$campaign->id] !!}</span>--}}
                {{--@else--}}
                {{--<span style="color:green">Good</span>--}}
                {{--@endif--}}
                {{--</td>--}}
                <td class="text-right">
                    @if(Auth::user()->type != 'staff')
                        <a href="/campaigns/set/{!! $campaign->id !!}?goto=/campaign/settings">settings</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>



@endsection
