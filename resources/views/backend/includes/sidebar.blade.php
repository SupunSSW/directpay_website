<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-title">
                @lang('menus.backend.sidebar.general')
            </li>

            <li class="nav-item">
                <a class="nav-link {{ active_class(Active::checkUriPattern('admin/dashboard')) }}"
                   href="{{ route('admin.dashboard') }}">
                    <i class="nav-icon icon-speedometer"></i> @lang('menus.backend.sidebar.dashboard')
                </a>
            </li>

            {{--            @if($logged_in_user->isAdmin() || $logged_in_user->can('View all insurance'))--}}
            {{--                <li class="nav-item">--}}
            {{--                    <a class="nav-link {{ active_class(Active::checkUriPattern('admin/transaction/firstTransaction')) }}"--}}
            {{--                       href="{{ route('admin.transaction.firstTransaction') }}">--}}
            {{--                        <i class="nav-icon icon-calendar"></i> Insurance--}}
            {{--                    </a>--}}
            {{--                </li>--}}
            {{--            @endif--}}

            {{--            @if($logged_in_user->isAdmin() || $logged_in_user->can('approve' ) || $logged_in_user->can('Edit' ))--}}
            {{--            <li class="nav-item">--}}
            {{--                <a class="nav-link {{ active_class(Active::checkUriPattern('admin/transaction/pendingTransaction')) }}"--}}
            {{--                   href="{{ route('admin.transaction.pendingTransaction') }}">--}}
            {{--                    <i class="nav-icon icon-hourglass"></i> Pending--}}
            {{--                </a>--}}
            {{--            </li>--}}
            {{--            @endif--}}

            @if($logged_in_user->isAdmin() || $logged_in_user->can('View all transactions'))
                <li class="nav-item">
                    <a class="nav-link {{ active_class(Active::checkUriPattern('admin/transaction/allTransactions')) }}"
                       href="{{ route('admin.transaction.allTransactions') }}">
                        <i class="nav-icon icon-loop"></i> All Transaction
                    </a>
                </li>
            @endif
            {{--            @if($logged_in_user->isAdmin() || $logged_in_user->can('View device'))--}}
            {{--                <li class="nav-item">--}}
            {{--                    <a class="nav-link {{ active_class(Active::checkUriPattern('admin/devices/listDevice')) }}"--}}
            {{--                       href="{{ route('admin.devices.listDevice') }}">--}}
            {{--                        <i class="nav-icon icon-phone"></i> Devices--}}
            {{--                    </a>--}}
            {{--                </li>--}}
            {{--            @endif--}}

            {{--            @if($logged_in_user->isAdmin() || $logged_in_user->can('View file upload'))--}}
            {{--                <li class="nav-item">--}}
            {{--                    <a class="nav-link {{ active_class(Active::checkUriPattern('admin/reports/fileUpload')) }}"--}}
            {{--                       href="{{ route('admin.reports.fileUpload') }}">--}}
            {{--                        <i class="nav-icon icon-notebook"></i> File upload--}}
            {{--                    </a>--}}
            {{--                </li>--}}
            {{--            @endif--}}


            @if($logged_in_user->isAdmin() || $logged_in_user->can('View reports'))
                <li class="nav-title">
                    Reports
                </li>
                @if($logged_in_user->isAdmin() || $logged_in_user->can('View Delivery Payments'))
                    <li class="nav-item">
                        <a class="nav-link {{ active_class(Active::checkUriPattern('admin/reports/delivery')) }}"
                           href="{{ route('admin.reports.delivery') }}">
                            <i class="nav-icon icon-folder"></i>Delivery Payments
                        </a>
                    </li>
                @endif
                {{--                @if($logged_in_user->isAdmin() || $logged_in_user->can('View cardExpire report'))--}}
                {{--                    <li class="nav-item">--}}
                {{--                        <a class="nav-link {{ active_class(Active::checkUriPattern('admin/reports/cardExp')) }}"--}}
                {{--                           href="{{ route('admin.reports.cardExp') }}">--}}
                {{--                            <i class="nav-icon icon-notebook"></i> Card Expiry--}}
                {{--                        </a>--}}
                {{--                    </li>--}}
                {{--                @endif--}}
                {{--                @if($logged_in_user->isAdmin() || $logged_in_user->can('View fileUpload report'))--}}
                {{--                    <li class="nav-item">--}}
                {{--                        <a class="nav-link {{ active_class(Active::checkUriPattern('admin/reports/uploads')) }}"--}}
                {{--                           href="{{ route('admin.reports.uploads') }}">--}}
                {{--                            <i class="nav-icon icon-notebook"></i> Uploads--}}
                {{--                        </a>--}}
                {{--                    </li>--}}
                {{--                @endif--}}
                {{--                @if($logged_in_user->isAdmin() || $logged_in_user->can('View statement report'))--}}
                {{--                    <li class="nav-item">--}}
                {{--                        <a class="nav-link {{ active_class(Active::checkUriPattern('admin/reports/statements')) }}"--}}
                {{--                           href="{{ route('admin.reports.statements') }}">--}}
                {{--                            <i class="nav-icon icon-note"></i> Settlement--}}
                {{--                        </a>--}}
                {{--                    </li>--}}
                {{--                @endif--}}
            @endif


            {{--            @if($logged_in_user->isAdmin() || $logged_in_user->can('View call center'))--}}
            {{--                <li class="nav-title">--}}
            {{--                    Call center--}}
            {{--                </li>--}}
            {{--                <li class="nav-item">--}}
            {{--                    <a class="nav-link {{ active_class(Active::checkUriPattern('admin/agent/policePayment')) }}"--}}
            {{--                       href="{{ route('admin.agent.policePayment') }}">--}}
            {{--                        <i class="nav-icon icon-link"></i> Policy Payment--}}
            {{--                    </a>--}}
            {{--                </li>--}}
            {{--            @endif--}}

{{--            @if($logged_in_user->isAdmin() || $logged_in_user->can('View call center'))--}}
{{--                <li class="nav-title">--}}
{{--                    Call center--}}
{{--                </li>--}}
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link {{ active_class(Active::checkUriPattern('admin/payment/newPayment')) }}"--}}
{{--                       href="{{ route('admin.payment.newPayment') }}">--}}
{{--                        <i class="nav-icon icon-link"></i> Payment Link--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            @endif--}}

            @if ($logged_in_user->isAdmin() || $logged_in_user->can('view userManagement'))
                <li class="nav-title">
                    @lang('menus.backend.sidebar.system')
                </li>


                <li class="nav-item nav-dropdown {{ active_class(Active::checkUriPattern('admin/auth*'), 'open') }}">
                    <a class="nav-link nav-dropdown-toggle {{ active_class(Active::checkUriPattern('admin/auth*')) }}"
                       href="#">
                        <i class="nav-icon icon-user"></i> @lang('menus.backend.access.title')

                        @if ($pending_approval > 0)
                            <span class="badge badge-danger">{{ $pending_approval }}</span>
                        @endif
                    </a>

                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/auth/user*')) }}"
                               href="{{ route('admin.auth.user.index') }}">
                                @lang('labels.backend.access.users.management')

                                @if ($pending_approval > 0)
                                    <span class="badge badge-danger">{{ $pending_approval }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ active_class(Active::checkUriPattern('admin/auth/role*')) }}"
                               href="{{ route('admin.auth.role.index') }}">
                                @lang('labels.backend.access.roles.management')
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <li class="divider"></li>

            @if($logged_in_user->isAdmin() || $logged_in_user->can('View logs'))
                <li class="nav-item nav-dropdown {{ active_class(Active::checkUriPattern('admin/log-viewer*'), 'open') }}">
                    <a class="nav-link nav-dropdown-toggle {{ active_class(Active::checkUriPattern('admin/log-viewer*')) }}"
                       href="#">
                        <i class="nav-icon icon-list"></i> @lang('menus.backend.log-viewer.main')
                    </a>

                    <ul class="nav-dropdown-items">
                        {{--                        @if($logged_in_user->isAdmin() || $logged_in_user->can('View activity log'))--}}
                        {{--                            <li class="nav-item">--}}
                        {{--                                <a class="nav-link {{ active_class(Active::checkUriPattern('admin/activityLog')) }}"--}}
                        {{--                                   href="{{ route('admin.activityLog') }}">--}}
                        {{--                                    Activity Log--}}
                        {{--                                </a>--}}
                        {{--                            </li>--}}
                        {{--                        @endif--}}
                        @if($logged_in_user->isAdmin() || $logged_in_user->can('View info log'))
                            <li class="nav-item">
                                <a class="nav-link {{ active_class(Active::checkUriPattern('admin/log-viewer')) }}"
                                   href="{{ route('log-viewer::dashboard') }}">
                                    @lang('menus.backend.log-viewer.dashboard')
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ active_class(Active::checkUriPattern('admin/log-viewer/logs*')) }}"
                                   href="{{ route('log-viewer::logs.list') }}">
                                    @lang('menus.backend.log-viewer.logs')
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

        </ul>
    </nav>

    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div><!--sidebar-->
