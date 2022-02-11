@extends('user.master',['menu'=>'profile'])
@section('title', isset($title) ? $title : '')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="tab-content cp-user-profile-tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active in " id="pills-profile"
                     role="tabpanel" aria-labelledby="pills-profile-tab">
                    <div class="cp-user-card-header-area">
                        <div class="title">
                            <h4 id="list_title">{{$user->first_name.' '.$user->last_name}}</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-4 mb-xl-0 mb-4">
                            <div class="card cp-user-custom-card">
                                <div class="card-body">
                                    <div class="user-profile-area">
                                        <div class="user-profile-img">
                                            <img src="{{show_image($user->id,'user')}}" class="img-fluid" alt="">
                                        </div>
                                        <div class="user-cp-user-profile-info">
                                            <h4>{{$user->first_name.' '.$user->last_name}}</h4>
                                            <p>{{$user->email}}</p>
                                            <p class="cp-user-btc">

                                            </p>
                                            <div class="cp-user-available-balance-profile">
                                                <p>{{user_trades_count($user->id,'total')}} {{__(' total trades')}}</p>
                                                <p>{{user_trades_count($user->id, TRADE_STATUS_TRANSFER_DONE)}} {{__(' successful trades')}}</p>
                                                <p>{{user_trades_count($user->id,'total') - (user_trades_count($user->id, TRADE_STATUS_TRANSFER_DONE) + user_trades_count($user->id, TRADE_STATUS_CANCEL))}} {{__(' ongoing trades')}}</p>
                                                <p>{{user_trades_count($user->id, TRADE_STATUS_CANCEL)}} {{__(' cancelled trades')}}</p>
                                                <p>{{user_disputed_trades($user->id)}} {{__(' disputed trades')}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8">
                            <div class="card cp-user-custom-card">
                                <div class="card-body">
                                    <div class="cp-user-profile-header">
                                        <h5>{{__('Profile Information')}}</h5>
                                    </div>
                                    <div class="cp-user-profile-info">
                                        <ul>
                                            <li>
                                                <span>{{__('Country')}}</span>
                                                <span class="cp-user-dot">:</span>
                                                <span>
                                                    @if(!empty($user->country))
                                                        {{countrylist(strtoupper($user->country))}}
                                                    @endif
                                                </span>
                                            </li>
                                            <li>
                                                <span>{{__('Email Verification')}}</span>
                                                <span class="cp-user-dot">:</span>
                                                <span>{{statusAction($user->is_verified)}}</span>
                                            </li>
                                            <li>
                                                <span>{{__('Phone')}}</span>
                                                <span class="cp-user-dot">:</span>
                                                <span>{{$user->phone}}</span>
                                            </li>
                                            <li>
                                                <span>{{__('Phone Verification')}}</span>
                                                <span class="cp-user-dot">:</span>
                                                <span class="pending">{{statusAction($user->phone_verified)}}</span>
                                            </li>

                                            <li>
                                                <span>{{__('Role')}}</span>
                                                <span class="cp-user-dot">:</span>
                                                <span>{{userRole($user->role)}}</span>
                                            </li>
                                            <li>
                                                <span>{{__('Active Status')}}</span>
                                                <span class="cp-user-dot">:</span>
                                                <span>{{statusAction($user->status)}}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <div class="cp-user-card-header-area">
                                <div class="title">
                                    <h4 id="list_title">{{__('Buy coin from these sellers')}}</h4>
                                </div>
                            </div>
                            <div class="cp-user-wallet-table table-responsive buy-table">
                                <table class="table">
                                    <thead>
                                    @if(isset($sells[0]))
                                        @foreach($sells as $sell)
                                            <tr>
                                                <th>
                                                    <p class="pb-0 mb-0 border-0"><a href="{{route('userTradeProfile',$sell->user_id)}}">{{$sell->user->first_name.' '.$sell->user->last_name}}</a></p>
                                                    <p class="pb-0 mb-0 border-0"> {{count_trades($sell->user->id)}} {{__(' trades')}}</p>
                                                </th>
                                                <th>
                                                    <p class="pb-0 mb-0 border-0">{{__('Coin type')}}</p>
                                                    <p class="pb-0 mb-0 border-0"> {{check_default_coin_type($sell->coin_type)}} </p>
                                                </th>
                                                <th>
                                                    <p class="pb-0 mb-0 border-0">{{__('Payment System')}}</p>
                                                    <p class="pb-0 mb-0 border-0">
                                                    @if(isset($sell->payment($sell->id)[0]))
                                                        <ul>
                                                            @foreach($sell->payment($sell->id) as $sell_payment)
                                                                @if($country == 'any')
                                                                    <li>
                                                                        <span><img width="25"  src="{{$sell_payment->payment_method->image}}" alt=""></span>
                                                                        {{ $sell_payment->payment_method->name}}
                                                                    </li>
                                                                @elseif(is_accept_payment_method($sell_payment->payment_method_id,$country))
                                                                    <li>
                                                                        <span><img width="25"  src="{{$sell_payment->payment_method->image}}" alt=""></span>
                                                                        {{ $sell_payment->payment_method->name}}
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                        @endif
                                                    </p>
                                                </th>
                                                <th>{{countrylist($sell->country)}}</th>
                                                <th>
                                                    {{number_format($sell->minimum_trade_size,2). ' '.$sell->currency }} {{__(' to ')}} {{number_format($sell->maximum_trade_size,2). ' '.$sell->currency }}
                                                </th>
                                                <th>
                                                    @if($sell->rate_type == RATE_TYPE_DYNAMIC)
                                                        <p class="pb-0 mb-0 border-0">{{number_format($sell->coin_rate,2).' '.$sell->currency}}</p>
                                                        <p class="pb-0 mb-0 border-0"> {{number_format($sell->rate_percentage,2)}} % {{price_rate_type($sell->price_type)}} {{__(' Market')}}</p>

                                                    @else
                                                        <p class="pb-0 mb-0 border-0">{{number_format($sell->coin_rate,2).' '.$sell->currency}}</p>
                                                        <p class="pb-0 mb-0 border-0">  {{__(' Static Rate')}}</p>
                                                    @endif
                                                </th>
                                                <th>
                                                    <a href="{{route('openTrade',['buy',$sell->id])}}"><button class="btn theme-btn">{{__('Buy Now')}}</button></a>
                                                </th>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <th colspan="7" class="text-center">{{__('No data available')}}</th>
                                        </tr>
                                    @endif
                                    </thead>
                                </table>
                                @if(isset($sells[0]))
                                    <div class="pull-right address-pagin">
                                        {{ $sells->appends(request()->input())->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <div class="cp-user-card-header-area">
                                <div class="title">
                                    <h4 id="list_title">{{__('Sell coin to these buyers')}}</h4>
                                </div>
                            </div>
                            <div class="cp-user-wallet-table table-responsive buy-table">
                                <table class="table">
                                    <tbody>
                                    @if(isset($buys[0]))
                                        @foreach($buys as $buy)
                                            <tr>
                                                <td>
                                                    <p class="pb-0 mb-0 border-0"><a href="{{route('userTradeProfile',$buy->user_id)}}">{{$buy->user->first_name.' '.$buy->user->last_name}}</a></p>
                                                    <p class="pb-0 mb-0 border-0"> {{count_trades($buy->user->id)}} {{__(' trades')}}</p>
                                                </td>
                                                <td>
                                                    <p class="pb-0 mb-0 border-0">{{__('Coin type')}}</p>
                                                    <p class="pb-0 mb-0 border-0"> {{check_default_coin_type($buy->coin_type)}} </p>
                                                </td>
                                                <td>
                                                    <p class="pb-0 mb-0 border-0">{{__('Payment System')}}</p>
                                                    <p class="pb-0 mb-0 border-0">
                                                    @if(isset($buy->payment($buy->id)[0]))
                                                        <ul>
                                                            @foreach($buy->payment($buy->id) as $buy_payment)
                                                                @if($country == 'any')
                                                                    <li>
                                                                        <span><img width="25" src="{{$buy_payment->payment_method->image}}" alt=""></span>
                                                                        {{ $buy_payment->payment_method->name}}
                                                                    </li>
                                                                @elseif(is_accept_payment_method($buy_payment->payment_method_id,$country))
                                                                    <li>
                                                                        <span><img width="25" src="{{$buy_payment->payment_method->image}}" alt=""></span>
                                                                        {{ $buy_payment->payment_method->name}}
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                        @endif
                                                        </p>
                                                </td>
                                                <td>{{countrylist($buy->country)}}</td>
                                                <td>
                                                    {{number_format($buy->minimum_trade_size,2). ' '.$buy->currency }} {{__(' to ')}} {{number_format($buy->maximum_trade_size,2). ' '.$buy->currency }}
                                                </td>
                                                <td>
                                                    @if($buy->rate_type == RATE_TYPE_DYNAMIC)
                                                        <p class="pb-0 mb-0 border-0">{{number_format($buy->coin_rate,2).' '.$buy->currency}}</p>
                                                        <p class="pb-0 mb-0 border-0"> {{number_format($buy->rate_percentage,2)}} % {{price_rate_type($buy->price_type)}} {{__(' Market')}}</p>

                                                    @else
                                                        <p class="pb-0 mb-0 border-0">{{number_format($buy->coin_rate,2).' '.$buy->currency}}</p>
                                                        <p class="pb-0 mb-0 border-0">  {{__(' Static Rate')}}</p>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{route('openTrade',['sell',$buy->id])}}"><button class="btn theme-btn">{{__('Sell Now')}}</button></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <th colspan="7" class="text-center">{{__('No data available')}}</th>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                                @if(isset($buys[0]))
                                    <div class="pull-right address-pagin">
                                        {{ $buys->appends(request()->input())->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
@endsection
