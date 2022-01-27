@extends('user.master',['menu'=>'buy_coin'])
@section('title', isset($title) ? $title : '')
@section('style')
    <style>
        .user-profile-img{
            height: auto !important;
            width: auto !important;
            border-radius: 0% !important;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card cp-user-custom-card">
                <div class="card-body">
                    <div class="cp-user-card-header-area">
                        <h4>{{__('Request submitted successful,please send ')}} {{$coinAddress->btc.' '.$coinAddress->coin_type}} {{__(' with this address')}}</h4>
                    </div>

                    <div class="cp-user-buy-coin-content-area mt-5">
                        <div class="cp-user-coin-info">
                            <div class="row align-items-center">
                                <div class="col-lg-3 col-md-4">
                                    <div class="qr-img">
                                        <div class="user-profile-area">
                                            <div class="user-profile-img">
                                                @if(isset($coinAddress->address))  {!! QrCode::size(300)->generate($coinAddress->address); !!} @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9 col-md-8">
                                    <div class="table-responsive">
                                        <table class="table qr-table">
                                            <tr>
                                                <td>
                                                    <h4 class="font-weight-normal font-16">{{__('Address')}} </h4>
                                                </td>
                                                <td>
                                                    <h4 class="font-weight-normal font-16">:</h4>
                                                </td>
                                                <td>
                                                    <h4 class="font-weight-normal font-16"> {{$coinAddress->address}} </h4>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h4 class="font-weight-normal font-16">{{__('Payable Coin')}} </h4>
                                                </td>
                                                <td>
                                                    <h4 class="font-weight-normal font-16">:</h4>
                                                </td>
                                                <td>
                                                    <h4 class="font-weight-normal font-16">{{$coinAddress->btc.' '.$coinAddress->coin_type}}</h4>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
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
