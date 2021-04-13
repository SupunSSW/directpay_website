@if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('View dashboard total transaction') || $logged_in_user->can('View dashboard total insurance'))

    <div class="bootstrap snippet">
        <div class="row">
            {{--            @if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('View dashboard total insurance'))--}}
            {{--                <div class="col">--}}
            {{--                    <div class="circle-tile ">--}}
            {{--                        <a href="#">--}}
            {{--                            <div class="circle-tile-heading dark-blue"><i--}}
            {{--                                    class="fas fa-address-card fa-fw fa-3x iconPos"></i></div>--}}
            {{--                        </a>--}}
            {{--                        <div class="circle-tile-content dark-gray">--}}
            {{--                            <div class="circle-tile-description text-faded"> Recurring Payment Registrations</div>--}}
            {{--                            <div class="circle-tile-number text-dark-blue" id="insCountData">--}}
            {{--                                <i class="fas fa-spinner fa-pulse dataLoader"></i>--}}
            {{--                            </div>--}}
            {{--                            @if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('View all insurance'))--}}
            {{--                                <a class="circle-tile-footer"--}}
            {{--                                   href="{{route('admin.transaction.firstTransaction')}}">--}}
            {{--                                    More Info--}}
            {{--                                    <i class="fa fa-chevron-circle-right"></i>--}}
            {{--                                </a>--}}
            {{--                            @endif--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            @endif--}}

            {{--            @if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('approve') || $logged_in_user->can('Edit'))--}}
            {{--                <div class="col">--}}
            {{--                    <div class="circle-tile ">--}}
            {{--                        <a href="#">--}}
            {{--                            <div class="circle-tile-heading dark-blue"><i--}}
            {{--                                    class="fas fa-hourglass fa-fw fa-3x iconPos"></i></div>--}}
            {{--                        </a>--}}
            {{--                        <div class="circle-tile-content dark-gray">--}}
            {{--                            <div class="circle-tile-description text-faded"> Pending / Approve</div>--}}
            {{--                            <div class="circle-tile-number text-dark-blue" id="pendCountData">--}}
            {{--                                <i class="fas fa-spinner fa-pulse dataLoader"></i>--}}
            {{--                            </div>--}}
            {{--                            @if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('View all insurance'))--}}
            {{--                                <a class="circle-tile-footer"--}}
            {{--                                   href="{{route('admin.transaction.pendingTransaction')}}">--}}
            {{--                                    More Info--}}
            {{--                                    <i class="fa fa-chevron-circle-right"></i>--}}
            {{--                                </a>--}}
            {{--                            @endif--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            @endif--}}

            @if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('View dashboard total transaction'))
                <div class="col">
                    <div class="circle-tile ">
                        <a href="#">
                            <div class="circle-tile-heading dark-blue"><i
                                    class="fas fa-calculator fa-fw fa-3x iconPos"></i></div>
                        </a>
                        <div class="circle-tile-content dark-gray">
                            <div class="circle-tile-description text-faded"> Total Transactions count</div>
                            <div class="circle-tile-number text-dark-blue" id="totTrnCountData">
                                <i class="fas fa-spinner fa-pulse dataLoader"></i>
                            </div>
                            @if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('View all transactions'))
                                <a class="circle-tile-footer"
                                   href="{{route('admin.transaction.allTransactions')}}">More Info
                                    <i class="fa fa-chevron-circle-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('View dashboard total transaction') || $logged_in_user->can('View dashboard total insurance'))
                <div class="col">
                    <div class="circle-tile ">
                        <a href="#">
                            <div class="circle-tile-heading dark-blue"><i
                                    class="fas fa-money-bill-alt fa-fw fa-3x iconPos"></i></div>
                        </a>
                        <div class="circle-tile-content dark-gray">
                            <div class="circle-tile-description text-faded"> Total Transaction Amount</div>
                            <div class="circle-tile-number text-dark-blue" id="totTrnAmountData">
                                <i class="fas fa-spinner fa-pulse dataLoader"></i>
                            </div>
                            @if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('View all transactions'))
                                <a class="circle-tile-footer"
                                   href="{{route('admin.transaction.allTransactions')}}">
                                    More Info
                                    <i class="fa fa-chevron-circle-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{--            @if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('View dashboard total device'))--}}
            <div class="col">
                <div class="circle-tile ">
                    <a href="#">
                        <div class="circle-tile-heading dark-blue"><i
                                class="fab iconPos fa-fw fa-3x fa-google-wallet"></i>
                        </div>
                    </a>
                    <div class="circle-tile-content dark-gray">
                        <div class="circle-tile-description text-faded"> Total Points</div>
                        <div class="circle-tile-number text-dark-blue" id="totPoints">
                            <i class="fas fa-spinner fa-pulse dataLoader"></i>
                        </div>
                        {{--                        @if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('View device'))--}}
                        {{--                            <a class="circle-tile-footer" href="{{route('admin.devices.listDevice')}}">More Info--}}
                        {{--                                <i class="fa fa-chevron-circle-right"></i>--}}
                        {{--                            </a>--}}
                        {{--                        @endif--}}
                    </div>
                </div>
            </div>
            {{--            @endif--}}
        </div>

        <div class="row">
            <div class="col">
                <div class="circle-tile ">
                    <a href="#">
                        <div class="circle-tile-heading dark-gray"><i
                                class="fas fa-link fa-fw fa-3x iconPos"></i>
                        </div>
                    </a>
                    <div class="circle-tile-content light-gray">
                        <div class="circle-tile-description text-faded"> Payment Link</div>
                        <div class="circle-tile-number text-dark-blue" id="toPaymentLink">
                            <i class="fas fa-spinner fa-pulse dataLoader"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="circle-tile ">
                    <a href="#">
                        <div class="circle-tile-heading dark-gray"><i
                                class="fas fa-qrcode fa-fw fa-3x iconPos"></i>
                        </div>
                    </a>
                    <div class="circle-tile-content light-gray">
                        <div class="circle-tile-description text-faded"> QR</div>
                        <div class="circle-tile-number text-dark-blue" id="toQR">
                            <i class="fas fa-spinner fa-pulse dataLoader"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="circle-tile ">
                    <a href="#">
                        <div class="circle-tile-heading dark-gray"><i
                                class="fas fa-money-bill-alt fa-fw fa-3x iconPos"></i>
                        </div>
                    </a>
                    <div class="circle-tile-content light-gray">
                        <div class="circle-tile-description text-faded"> Cash</div>
                        <div class="circle-tile-number text-dark-blue" id="toCash">
                            <i class="fas fa-spinner fa-pulse dataLoader"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endif

