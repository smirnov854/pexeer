<!DOCTYPE HTML>
<html class="no-js" lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="{{allsetting('app_title')}}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{allsetting('app_title')}}"/>
    <meta property="og:image" content="{{show_image(Auth::user()->id,'logo')}}">
    <meta property="og:site_name" content="{{allsetting('app_title')}}"/>
    <meta property="og:url" content="{{url()->current()}}"/>
    <meta itemprop="image" content="{{show_image(Auth::user()->id,'logo')}}" />
{{--    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">--}}
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('assets/common/css/bootstrap.min.css')}}">
    <!-- metismenu CSS -->
    <link rel="stylesheet" href="{{asset('assets/common/css/metisMenu.min.css')}}">
    <!-- fontawesome CSS -->
    <link rel="stylesheet" href="{{asset('assets/common/css/font-awesome.min.css')}}">
    {{--for toast message--}}
    <link href="{{asset('assets/common/toast/vanillatoasts.css')}}" rel="stylesheet" >
    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{asset('assets/common/css/datatable/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/common/css/datatable/dataTables.bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/common/css/datatable/dataTables.jqueryui.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/common/css/datatable/dataTables.responsive.css')}}">
    <link rel="stylesheet" href="{{asset('assets/common/css/datatable/jquery.dataTables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/common/css/css-circular-prog-bar.css')}}">
    {{-- datepicker --}}
    <link rel="stylesheet" href="{{asset('assets/common/datepicker/css/bootstrap-datepicker.min.css')}}">

    {{--    dropify css  --}}
    <link rel="stylesheet" href="{{asset('assets/common/dropify/dropify.css')}}">
    {{--    for search with tag--}}
    <link href="{{asset('assets/common/multiselect/tokenize2.css')}}" rel="stylesheet">
    <!-- select -->
    <link rel="stylesheet" href="{{asset('assets/common/multiselect/bootstrap-select.min.css')}}">

    {{-- summernote --}}
    <link rel="stylesheet" href="{{asset('assets/common/summernote/summernote.min.css')}}">
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{asset('assets/admin/style.css')}}">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/responsive.css')}}">
    @yield('style')
    <title>@yield('title')</title>
    <!-- Favicon and Touch Icons -->
    <link rel="shortcut icon" href="{{landingPageImage('favicon','images/Pexeer.svg')}}">
    <script src="{{asset('assets/common/js/jquery.min.js')}}"></script>
</head>

<body class="body-bg">
<!-- Start sidebar -->
<div class="sidebar">
    <!-- logo -->
    <div class="logo">
        <a href="{{route('adminDashboard')}}">
            <img src="{{show_image(Auth::user()->id,'logo')}}" class="img-fluid" alt="">
        </a>
    </div><!-- /logo -->

    <!-- sidebar menu -->
    <div class="sidebar-menu">
        <nav>
            <ul id="metismenu">
                <li class="@if(isset($menu) && $menu == 'dashboard') active-page @endif">
                    <a href="{{route('adminDashboard')}}">
                        <span class="icon"><img src="{{asset('assets/admin/images/sidebar-icons/dashboard.svg')}}" class="img-fluid" alt=""></span>
                        <span class="name">{{__('Dashboard')}}</span>
                    </a>
                </li>
                <li class="@if(isset($menu) && $menu == 'users') active-page @endif">
                    <a href="#" aria-expanded="true">
                        <span class="icon"><img src="{{asset('assets/admin/images/sidebar-icons/user.svg')}}" class="img-fluid" alt=""></span>
                        <span class="name">{{__('User Management')}}</span>
                    </a>
                    <ul class="@if(isset($menu) && $menu == 'users')  mm-show  @endif">
                        <li class="@if(isset($sub_menu) && $sub_menu == 'user') submenu-active @endif">
                            <a href="{{route('adminUsers')}}">{{__('User')}}</a>
                        </li>
                        <li class="@if(isset($sub_menu) && $sub_menu == 'pending_id') submenu-active @endif">
                            <a href="{{route('adminUserIdVerificationPending')}}">{{__('Pending ID Verification')}}</a>
                        </li>
                    </ul>
                </li>
                <li class="@if(isset($menu) && $menu == 'coin') active-page  @endif">
                    <a href="#" aria-expanded="true">
                        <span class="icon">
                            <img src="{{asset('assets/admin/images/sidebar-icons/coin.svg')}}" class="img-fluid" alt="">
                        </span>
                        <span class="name">{{__('Coin')}}</span>
                    </a>
                    <ul class="@if(isset($menu) && $menu == 'coin')  mm-show  @endif">
                        <li class="@if(isset($sub_menu) && $sub_menu == 'coin') submenu-active  @endif"><a href="{{route('adminCoinList')}}">{{__('Coin List')}}</a></li>
                        @if(admin_feature_enable('buy_coin_feature'))
                            <li class="@if(isset($sub_menu) && $sub_menu == 'phase') submenu-active @endif">
                                <a href="{{route('adminPhaseList')}}">{{__('Ico Phase List')}}</a>
                            </li>
                        @endif
                    </ul>
                </li>
                @if(admin_feature_enable('buy_coin_feature'))
                    <li class="@if(isset($menu) && $menu == 'buy_coin') active-page  @endif">
                        <a href="#" aria-expanded="true">
                            <span class="icon">
                                <img src="{{asset('assets/user/images/sidebar-icons/coin.svg')}}" class="img-fluid" alt="">
                            </span>
                            <span class="name">{{__('Buy Coin')}}</span>
                        </a>
                        <ul class="@if(isset($menu) && $menu == 'buy_coin')  mm-show  @endif">
                            <li class="@if(isset($sub_menu) && $sub_menu == 'buy_coin') submenu-active  @endif"><a href="{{route('adminPendingCoinOrder')}}">{{__('Buy Coin List')}}</a></li>
                        </ul>
                    </li>
                @endif
                <li class="@if(isset($menu) && $menu == 'pocket') active-page @endif">
                    <a href="{{route('adminWalletList')}}">
                        <span class="icon"><img src="{{asset('assets/admin/images/sidebar-icons/wallet.svg')}}" class="img-fluid" alt=""></span>
                        <span class="name">{{__('Wallet List')}}</span>
                    </a>
                </li>
                <li class="@if(isset($menu) && $menu == 'transaction') active-page @endif">
                    <a href="#" aria-expanded="true">
                        <span class="icon"><img src="{{asset('assets/admin/images/sidebar-icons/Transaction-1.svg')}}" class="img-fluid" alt=""></span>
                        <span class="name">{{__('Transaction History')}}</span>
                    </a>
                    <ul class="@if(isset($menu) && $menu == 'transaction')  mm-show  @endif">
                        <li class="@if(isset($sub_menu) && $sub_menu == 'transaction_all') submenu-active @endif">
                            <a href="{{route('adminTransactionHistory')}}">{{__('All Transaction')}}</a>
                        </li>
                        <li class="@if(isset($sub_menu) && $sub_menu == 'transaction_withdrawal') submenu-active @endif">
                            <a href="{{route('adminPendingWithdrawal')}}">{{__('Pending Withdrawal')}}</a>
                        </li>
                        <li class="@if(isset($sub_menu) && $sub_menu == 'gas_sent') submenu-active @endif">
                            <a href="{{route('adminGasSendHistory')}}">{{__('Gas Sent History')}}</a>
                        </li>
                        <li class="@if(isset($sub_menu) && $sub_menu == 'receive_token') submenu-active @endif">
                            <a href="{{route('adminTokenReceiveHistory')}}">{{__('Token Receive History')}}</a>
                        </li>
                    </ul>
                </li>
                <li class="@if(isset($menu) && $menu == 'offer') active-page  @endif">
                    <a href="#" aria-expanded="true">
                        <span class="icon">
                            <img src="{{asset('assets/user/images/sidebar-icons/My-Offer-1.svg')}}" class="img-fluid" alt="">
                        </span>
                        <span class="name">{{__('Offer List')}}</span>
                    </a>
                    <ul class="@if(isset($menu) && $menu == 'offer')  mm-show  @endif">
                        <li class="@if(isset($sub_menu) && $sub_menu == 'buy_offer') submenu-active @endif"><a href="{{route('offerList',BUY)}}">{{__('Buy Offer')}}</a></li>
                        <li class="@if(isset($sub_menu) && $sub_menu == 'sell_offer') submenu-active @endif"><a href="{{route('offerList',SELL)}}">{{__('Sell Offer')}}</a></li>
                    </ul>
                </li>
                <li class="@if(isset($menu) && $menu == 'order') active-page  @endif">
                    <a href="#" aria-expanded="true">
                        <span class="icon">
                            <img src="{{asset('assets/user/images/sidebar-icons/Trade-1.svg')}}" class="img-fluid" alt="">
                        </span>
                        <span class="name">{{__('Trade')}}</span>
                    </a>
                    <ul class="@if(isset($menu) && $menu == 'order')  mm-show  @endif">
                        <li class="@if(isset($sub_menu) && $sub_menu == 'order') submenu-active  @endif"><a href="{{route('orderList')}}">{{__('Trade List')}}</a></li>
                        <li class="@if(isset($sub_menu) && $sub_menu == 'dispute') submenu-active @endif"><a href="{{route('orderDisputeList')}}">{{__('Dispute List')}}</a></li>
                    </ul>
                </li>
                <li class="@if(isset($menu) && $menu == 'payment_method') active-page @endif">
                    <a href="{{ route('paymentMethodList') }}">
                        <span class="icon"><img src="{{asset('assets/user/images/sidebar-icons/Payment-method-1.svg')}}" class="img-fluid" alt=""></span>
                        <span class="name">{{__('Payment Method')}}</span>
                    </a>
                </li>
                <li class="@if(isset($menu) && $menu == 'notification') active-page @endif">

                    <a href="#" aria-expanded="true">
                        <span class="icon"><img src="{{asset('assets/admin/images/sidebar-icons/Notification.svg')}}" class="img-fluid" alt=""></span>
                        <span class="name">{{__('Notification')}}</span>
                    </a>
                    <ul class="@if(isset($menu) && $menu == 'notification')  mm-show  @endif">
                        <li class="@if(isset($sub_menu) && $sub_menu == 'notify') submenu-active @endif">
                            <a href="{{route('sendNotification')}}">{{__('Notification')}}</a>
                        </li>
                        <li class="@if(isset($sub_menu) && $sub_menu == 'email') submenu-active @endif">
                            <a href="{{route('sendEmail')}}">{{__('Bulk Email')}}</a>
                        </li>
                    </ul>
                </li>
                <li class="@if(isset($menu) && $menu == 'setting') active-page @endif">
                    <a href="#" aria-expanded="true">
                        <span class="icon"><img src="{{asset('assets/admin/images/sidebar-icons/settings.svg')}}" class="img-fluid" alt=""></span>
                        <span class="name">{{__('Settings')}}</span>
                    </a>
                    <ul class="@if(isset($menu) && $menu == 'setting')  mm-show  @endif">
                        <li class="@if(isset($sub_menu) && $sub_menu == 'feature') submenu-active @endif">
                            <a href="{{route('adminFeatureSettings')}}">{{__('Feature Settings')}}</a>
                        </li>
                        <li class="@if(isset($sub_menu) && $sub_menu == 'general') submenu-active @endif">
                            <a href="{{route('adminSettings')}}">{{__('General Settings')}}</a>
                        </li>
{{--                        <li class="@if(isset($sub_menu) && $sub_menu == 'landing') submenu-active @endif">--}}
{{--                            <a href="{{route('landingSettings')}}">{{__('Landing Settings')}}</a>--}}
{{--                        </li>--}}
                        <li class="@if(isset($sub_menu) && $sub_menu == 'custom_pages') submenu-active @endif">
                            <a href="{{ route('adminCustomPageList') }}">{{__('Custom Pages')}}</a>
                        </li>
                        <li class="@if(isset($sub_menu) && $sub_menu == 'landing') submenu-active @endif">
                            <a href="{{route('landingPageSettings')}}">{{__('Landing Page Settings')}}</a>
                        </li>
                        <li class="@if(isset($sub_menu) && $sub_menu == 'footer_setting') submenu-active @endif">
                            <a href="{{route('landingPageFooterSettings')}}">{{__('Footer Settings')}}</a>
                        </li>
                        @if(admin_feature_enable('buy_coin_feature'))
                            <li class="@if(isset($sub_menu) && $sub_menu == 'bank') submenu-active @endif">
                                <a href="{{ route('bankList') }}">{{__('Bank Settings')}}</a>
                            </li>
                        @endif
                        <li class="@if(isset($sub_menu) && $sub_menu == 'testimonial') submenu-active @endif">
                            <a href="{{route('adminTestimonialList')}}">{{__('Testimonial')}}</a>
                        </li>
                        <li class="@if(isset($sub_menu) && $sub_menu == 'subscribers') submenu-active @endif">
                            <a href="{{route('subscribers')}}">{{__('Subscribers')}}</a>
                        </li>
                        <li class="@if(isset($sub_menu) && $sub_menu == 'faq') submenu-active @endif">
                            <a href="{{ route('adminFaqList') }}">{{__('FAQ')}}</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div><!-- /sidebar menu -->

</div>
<!-- End sidebar -->
<!-- top bar -->
<div class="top-bar">
    <div class="container-fluid">
        <div class="row align-items-center justify-content-between">
            <div class="col-xl-1 col-md-2 col-3 top-bar-logo top-bar-logo-hide">
                <div class="logo">
                    <a href="{{route('adminDashboard')}}"><img src="{{show_image(Auth::user()->id,'logo')}}" class="img-fluid logo-large" alt=""></a>
                    <a href="{{route('adminDashboard')}}"><img src="{{show_image(Auth::user()->id,'logo')}}" class="img-fluid logo-small" alt=""></a>
                </div>
            </div>
            <div class="col-xl-1 col-md-2 col-3">
                <div class="menu-bars">
                    <img src="{{asset('assets/admin/images/sidebar-icons/menu.svg')}}" class="img-fluid" alt="">
                </div>
            </div>
            <div class="col-xl-10 col-md-8 col-6">
                <div class="top-bar-right">
                    <ul>
                        <li>
                            <div class="btn-group profile-dropdown">
                                <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="cp-user-avater">
                                        <span class="cp-user-img">
                                            <img src="{{show_image(Auth::user()->id,'user')}}" class="img-fluid" alt="">
                                        </span>
                                        <span class="name">{{Auth::user()->first_name.' '.Auth::user()->last_name}}</span>
                                    </span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <span class="big-user-thumb">
                                        <img src="{{show_image(Auth::user()->id,'user')}}" class="img-fluid" alt="">
                                    </span>
                                    <div class="user-name">
                                        <p>{{Auth::user()->first_name.' '.Auth::user()->last_name}}</p>
                                    </div>
                                    <button class="dropdown-item" type="button"><a href="{{route('adminProfile')}}"><i class="fa fa-user-circle-o"></i> {{__('Profile')}}</a></button>
                                    <button class="dropdown-item" type="button"><a href="{{route('logOut')}}"><i class="fa fa-sign-out"></i> {{__('Logout')}}</a></button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /top bar -->

<!-- main wrapper -->
<div class="main-wrapper">
    <div class="container-fluid">
        @yield('content')
    </div>
</div>
<!-- /main wrapper -->

<!-- js file start -->

<!-- JavaScript -->
<script src="{{asset('assets/common/js/jquery.min.js')}}"></script>
<script src="{{asset('assets/common/js/popper.min.js')}}"></script>
<script src="{{asset('assets/common/js/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/common/js/metisMenu.min.js')}}"></script>


<script src="{{asset('assets/common/js/apexcharts.min.js')}}"></script>
<script src="{{asset('assets/common/js/jquery.circlechart.js')}}"></script>

{{--toast message--}}
<script src="{{asset('assets/common/toast/vanillatoasts.js')}}"></script>
<script src="{{asset('assets/common/sweetalert/sweetalert.min.js')}}"></script>
<!-- Datatable -->
<script src="{{asset('assets/common/js/datatable/datatables.min.js')}}"></script>
<script src="{{asset('assets/common/js/datatable/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/common/js/datatable/dataTables.jqueryui.min.js')}}"></script>
<script src="{{asset('assets/common/js/datatable/dataTables.responsive.js')}}"></script>
<script src="{{asset('assets/common/js/datatable/jquery.dataTables.min.js')}}"></script>

<script src="{{asset('assets/common/datepicker/js/bootstrap-datepicker.min.js')}}"></script>

{{-- summernote --}}
<script src="{{asset('assets/common/summernote/summernote.min.js')}}"></script>
{{--for select--}}
<script src="{{asset('assets/common/multiselect/bootstrap-select.min.js')}}"></script>
{{--dropify--}}
<script src="{{asset('assets/common/dropify/dropify.js')}}"></script>
<script src="{{asset('assets/common/dropify/form-file-uploads.js')}}"></script>

<script src="{{asset('assets/admin/js/main.js')}}"></script>
{{--    for search with tag--}}
<script src="{{asset('assets/common/multiselect/tokenize2.js')}}"></script>


<script>
    (function($) {
        "use strict";

        @if(session()->has('success'))
            swal({
                text: '{{ session('success') }}',
                icon: "success",
                buttons: false,
                timer: 3000,
            });

        @elseif(session()->has('dismiss'))
            swal({
                text: '{{ session('dismiss') }}',
                icon: "warning",
                buttons: false,
                timer: 3000,
            });

        @elseif($errors->any())
            @foreach($errors->getMessages() as $error)
                swal({
                    text: '{{ $error[0] }}',
                    icon: "error",
                    buttons: false,
                    timer: 3000,
                });
            @break
            @endforeach
        @endif

        $('#btEditor').summernote({height: 300});
        $('#btEditor2').summernote({height: 400});
        $('#btEditor3').summernote({height: 400});
        $('#btEditor4').summernote({height: 400});
    })(jQuery);
</script>

@yield('script')
</body>
</html>

