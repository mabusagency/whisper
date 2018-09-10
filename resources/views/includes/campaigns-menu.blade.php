<div class="campaign-back-link">
    <i>
    <a href="/campaigns">
        <span class="glyphicon glyphicon glyphicon-circle-arrow-left"></span>
        Go Back
    </a>
    </i>
</div>
<h3>
    {!! session('campaign')->name !!}
</h3>

<ul class="nav-content hidden-sm hidden-xs">
    @if(Auth::user()->type != 'staff')
        <li role="presentation" class="@if(strstr(Request::path(),'student')) active @endif"><a
                    href="/campaign/students">Students</a></li>
        <li role="presentation" class="@if(strstr(Request::path(),'results')) active @endif"><a
                    href="/campaign/results">Results</a></li>
        @if(Auth::user()->type == 'admin')
            <li role="presentation" class="@if(strstr(Request::path(),'settings')) active @endif"><a
                        href="/campaign/settings">Settings</a></li>
        @endif
    @endif
</ul>













