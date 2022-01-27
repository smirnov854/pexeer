@extends('user.master',[ 'menu'=>'marketplace'])
@section('title', isset($title) ? $title : '')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12 mb-xl-0 mb-4">
            <div class="card cp-user-custom-card">
                <div class="card-body">
                    <div class="cp-user-buy-coin-content-area">
                        <div class="cp-user-coin-info">
                            <div class="row mt-4">
                                <div class="col-sm-12">
                                    <div class="cp-user-card-header-area">
                                        <div class="title">
                                            <h4 id="list_title"><a href="{{route('marketPlace')}}">{{__(' Offer ')}}</a> -> {{$type_text}}  <a href="{{route('userTradeProfile',$offer->user_id)}}">{{$offer->user->first_name.' '.$offer->user->last_name}}</a></h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <form action="{{route('placeOrder')}}" method="POST" enctype="multipart/form-data"
                                                  id="buy_coin">
                                                @csrf
                                            <div class="cp-user-card-header-area">
                                                <div class="title">
                                                    <h4 id="list_title">{{__('Open Trade')}}</h4>
                                                </div>
                                            </div>
                                            <div class="cp-user-payment-type">
                                                <input type="hidden" name="type" value="{{$type}}">
                                                <input type="hidden" name="offer_id" value="{{$offer->id}}">
                                                <h3>{{__('Trade Amount ')}} <span>{{number_format($offer->minimum_trade_size,2). ' '.$offer->currency }} {{__(' to ')}} {{number_format($offer->maximum_trade_size,2). ' '.$offer->currency }}</span></h3>
                                                <label class="text-warning">{{$offer->currency}}</label><input name="price" id="tradeprice" class="form-control" placeholder="">
                                                <br/>
                                                <span class="text-danger"><strong>{{ $errors->first('price') }}</strong></span>
                                                <label class="text-warning">{{check_default_coin_type($offer->coin_type)}}</label><input name="amount" id="tradeamount" class="form-control" placeholder="">
                                                <span class="text-danger"><strong>{{ $errors->first('amount') }}</strong></span>
                                            </div>
                                            <div class="cp-user-payment-type">
                                                <h3>{{__('Select Payment Method')}}</h3>
                                                @if(isset($offer->payment($offer->id)[0]))
                                                    @foreach($offer->payment($offer->id) as $pmethod)
                                                        <div class="form-group">
                                                            @if(is_accept_payment_method($pmethod->payment_method_id,$country))
                                                                <input type="radio"  @if(old('payment_id') == $pmethod->payment_method_id) checked @endif  value="{{$pmethod->payment_method_id}}" id='{{"coin-option".$pmethod->payment_method_id}}' name="payment_id">
                                                                <label for='{{"coin-option".$pmethod->payment_method_id}}'>{{$pmethod->payment_method->name}}</label>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                                <span class="text-danger"><strong>{{ $errors->first('payment_id') }}</strong></span>
                                            </div>
                                            <div class="cp-user-payment-type">
                                                <h3>{{__('Send Message ')}} </h3>
                                                <textarea name="text_message" class="form-control" id="" cols="30" rows="10" placeholder="{{__('Say hello,. Traders use encrypted messages to exchange payment details.')}}">{{old('text_message')}}</textarea>
                                            </div>
                                                <button id="buy_button" type="submit" class="mt-4 btn theme-btn">{{__('Open Trade')}}</button>
                                            <p class="mt-2">
                                                <span class="text-warning"><b>{{__('Note : ')}}</b></span>
                                                <span class="">{{__('Once you open a trade, messages are end-to-end encrypted so your privacy is protected. The only case where we can read your messages is if either party initiates a dispute. ')}}</span>
                                            </p>

                                            </form>
                                        </div>
                                        <div class="col-xl-1"></div>
                                        <div class="col-xl-7">
                                            <div class="cp-user-card-header-area">
                                                <div class="title">
                                                    <h4 id="list_title">{{__('You are the ')}}{{$type == 'buy' ? __('Buyer') : __('Seller')}}</h4>
                                                </div>
                                            </div>
                                            <div class="cp-user-payment-type available-payment-method-list">
                                                <h3>{{__('Available Payment Method')}} </h3>
                                                @if(isset($offer->payment($offer->id)[0]))
                                                    @foreach($offer->payment($offer->id) as $pmethod)
                                                        <ul>
                                                            @if(is_accept_payment_method($pmethod->payment_method_id,$country))
                                                                <li class="text-warning">
                                                                    <span><img width="25" src="{{$pmethod->payment_method->image}}" alt=""></span>
                                                                    {{ $pmethod->payment_method->name}}
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    @endforeach
                                                @endif

                                                <h3 class="mt-5 ">
                                                   <span class="text-center">
                                                       1 {{$offer->coin_type}} = {{number_format($offer->coin_rate,2)}}{{' '.$offer->currency}}
                                                   </span>
                                                </h3>
                                                <p class="mt-1 ">
                                                    {{__('The ')}} {{$type == 'buy' ? __('Buyer') : __('Seller')}} {{__(' chose this price — only continue if you’re comfortable with it.')}}
                                                </p>
                                            </div>
                                            <div class="row">
                                                <div class="col-xl-4">
                                                    <div class="cp-user-card-header-area">
                                                        <div class="title">
                                                            <h4 id="list_title">{{__('About the ')}}{{$type == 'sell' ? __('Buyer') : __('Seller')}}</h4>
                                                        </div>
                                                    </div>
                                                    <ul class="about-seller">
                                                        <li>
                                                            <a href="{{route('userTradeProfile',$offer->user_id)}}">{{$offer->user->first_name.' '.$offer->user->last_name}}</a>
                                                        </li>
                                                        <li>{{__('100% good feedback')}}</li>
                                                        <li>{{__('Registered ')}} {{date('M Y', strtotime($offer->user->created_at))}}</li>
                                                        <li>{{count_trades($offer->user_id)}} {{__(' trades')}} </li>
                                                    </ul>
                                                </div>
                                                <div class="col-xl-8">
                                                    <div class="cp-user-card-header-area">
                                                        <div class="title">
                                                            <h4 id="list_title">{{__('Headline ')}}</h4>
                                                        </div>
                                                    </div>
                                                    <p>{{ $offer->headline }}</p>

                                                    <div class="cp-user-card-header-area">
                                                        <div class="title">
                                                            <h4 id="list_title">{{__('Terms and Condition ')}}</h4>
                                                        </div>
                                                    </div>
                                                    <p>{{ isset($offer->terms) ? $offer->terms : '.....................................'}}</p>

                                                    <div class="cp-user-card-header-area">
                                                        <div class="title">
                                                            <h4 id="list_title">{{__('Trading Instruction')}}</h4>
                                                        </div>
                                                    </div>
                                                    <p>{{ isset($offer->instruction) ? $offer->instruction : '.....................................'}}</p>
                                                </div>
                                            </div>

                                        </div>
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
    <script>
        function delay(callback, ms) {
            var timer = 0;
            return function () {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback.apply(context, args);
                }, ms || 0);
            };
        }

        function call_trade_coin_rate(amount, type, order_type, offer_id) {

            $.ajax({
                type: "POST",
                url: "{{ route('getTradeCoinRate') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'amount': amount,
                    'type': type,
                    'order_type': order_type,
                    'offer_id': offer_id,
                },
                dataType: 'JSON',

                success: function (data) {
                    $('#tradeamount').val(data.amount)
                    $('#tradeprice').val(data.price)
                },
                error: function () {

                }
            });
        }

        $("#tradeamount").on('keyup', delay(function (e) {
            var amount = $('input[name=amount]').val();
            var type = 'same';
            var order_type = '{{$type}}';
            var offer_id = '{{$offer->id}}';

            call_trade_coin_rate(amount, type, order_type, offer_id);

        }, 500));

        $("#tradeprice").on('keyup', delay(function (e) {
            var amount = $('input[name=price]').val();
            var type = 'reverse';
            var order_type = '{{$type}}';
            var offer_id = '{{$offer->id}}';

            call_trade_coin_rate(amount, type, order_type, offer_id);

        }, 500));
    </script>
@endsection
