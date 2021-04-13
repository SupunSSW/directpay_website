<div class="row">
    <div class="col">
        <div class="float-left">
            <select class="selectpicker show-tick" data-style="btn-info" id="dashCategSelect">
                <option value="">Select duration...</option>
                @if($logged_in_user->hasRole(['administrator']))
                    <option value="0">All</option>
                    <option value="1" selected>Today</option>
                    <option value="2">This Week</option>
                    <option value="3">This Month</option>
                    <option value="4">This Year</option>
                @endif

                @if($logged_in_user->can('Select dashboard All'))
                    <option value="0">All</option>
                @endif
                @if($logged_in_user->can('Select dashboard Today'))
                    <option value="1">Today</option>
                @endif
                @if($logged_in_user->can('Select dashboard Week'))
                    <option value="2">This Week</option>
                @endif
                @if($logged_in_user->can('Select dashboard Month'))
                    <option value="3">This Month</option>
                @endif
                @if($logged_in_user->can('Select dashboard Year'))
                    <option value="4">This Year</option>
                @endif

            </select>
        </div>
{{--        @if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('View settings'))--}}
{{--            <div class="float-right">--}}
{{--                <div class="dropdown">--}}
{{--                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"--}}
{{--                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"--}}
{{--                            title="Settings">--}}
{{--                        <i class="icon-settings fa-2x"></i>--}}
{{--                    </button>--}}
{{--                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">--}}

{{--                        <div class="card-body">--}}
{{--                            <label for="linkGenerate">Create Policy</label>--}}
{{--                            <br/>--}}
{{--                            <input id="linkGenerateToggle" name="linkGenerate" type="checkbox"--}}
{{--                                   @if($link == 'true') checked--}}
{{--                                   @endif data-toggle="toggle"--}}
{{--                                   data-width="100" data-height="20"--}}
{{--                                   data-on="Enable" data-off="Disable" data-onstyle="success"--}}
{{--                                   data-offstyle="primary">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endif--}}
    </div>
</div>
