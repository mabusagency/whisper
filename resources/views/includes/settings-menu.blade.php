<div class="panel panel-default panel-flush">
    <div class="panel-heading">
        Settings
    </div>

    <div class="panel-body">
        <div class="spark-settings-tabs">
            <ul class="nav spark-settings-stacked-tabs" role="tablist">
                <!-- Profile Link -->
                <li role="presentation" class="@if(Request::path() == 'app/settings/profile') active @endif">

                    <a href="/settings/profile" aria-controls="profile">
                        <i class="fa fa-fw fa-btn fa-edit"></i>Profile
                    </a>
                </li>

                <!-- Security Link -->
                <li role="presentation" class="@if(Request::path() == 'app/settings/security') active @endif">
                    <a href="/settings/security" aria-controls="security">
                        <i class="fa fa-fw fa-btn fa-lock"></i>Security
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>