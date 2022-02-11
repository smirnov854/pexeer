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
                            <form action="{{route('marketPlace')}}" method="GET" enctype="multipart/form-data" id="">
                                @csrf
                                <div class="row align-items-end">
                                <div class="col-xl-2 col-sm-4 col-lg-4">
                                    <div class="cp-user-payment-type mb-3">
                                        <h3>{{__('Want to')}}</h3>
                                        <select name="offer_type" class=" form-control" >
                                            @foreach(buy_sell() as $key => $value)
                                                <option @if(isset($offer_type) && $offer_type == $key) selected @endif value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-sm-4 col-lg-4">
                                    <div class="cp-user-payment-type mb-3">
                                        <h3>{{__('Coin Type')}}</h3>
                                        <select name="coin_type" class=" form-control" >
                                            @if(isset($coins[0]))
                                                @foreach($coins as $coin)
                                                    <option @if(isset($coins_type) && $coins_type == $coin->type) selected @endif value="{{$coin->type}}">{{check_default_coin_type($coin->type)}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-sm-4 col-lg-4">
                                    <div class="cp-user-payment-type mb-3">
                                        <h3>{{__('Location')}}</h3>
                                        <select name="country" class=" form-control" id="country">
                                            <option value="any">{{__('Anywhere')}}</option>
                                            @foreach($countries as $key => $value)
                                                <option @if(isset($country) && $country == $key) selected @endif value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-sm-4 col-lg-4">
                                    <div class="cp-user-payment-type mb-3" id="">
                                        <h3>{{__('Payment Method')}}</h3>
                                        <select name="payment_method" class=" form-control" id="select-payment-method" >
                                            <option value="any">{{__('Any Payment Method')}}</option>
                                            @if(isset($payment_methods[0]))
                                                @foreach($payment_methods as $payment_method)
                                                    @if($country == 'any')
                                                        <option @if(isset($pmethod) && $pmethod == $payment_method->id) selected @endif value="{{$payment_method->id}}">{{$payment_method->name}}</option>
                                                    @elseif(is_accept_payment_method($payment_method->id,$country))
                                                        <option @if(isset($pmethod) && $pmethod == $payment_method->id) selected @endif value="{{$payment_method->id}}">{{$payment_method->name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-sm-4 col-lg-4">
                                    <div class="cp-user-payment-type text-right mr-3 mb-3">
                                        <h3></h3>
                                        <button type="submit" class="btn theme-btn">{{__('Filter')}}</button>
                                    </div>
                                </div>

                            </div>
                            </form>
                            <div class="row mt-4">
                                <div class="col-sm-12">
                                    <div class="cp-user-card-header-area">
                                        <div class="title">
                                            <h4 id="list_title">{{__('Buy ')}} {{check_default_coin_type($coins_type)}} {{__(' from these sellers')}}</h4>
                                        </div>
                                    </div>
                                    <div class="cp-user-wallet-table table-responsive buy-table crypto-exchange-table">
                                        <table class="table">
                                            <tbody>
                                            @if(isset($sells[0]))
                                                @foreach($sells as $sell)
                                                    <tr>
                                                        <td>
                                                            <h4><a href="{{route('userTradeProfile',$sell->user_id)}}">{{$sell->user->first_name.' '.$sell->user->last_name}}</a></h4>
                                                            <p> {{count_trades($sell->user->id)}} {{__(' trades')}}</p>
                                                        </td>
                                                        <td>
                                                            <h4>{{__('Payment System')}}</h4>
                                                                @if(isset($sell->payment($sell->id)[0]))
                                                                <ul>
                                                                    @foreach($sell->payment($sell->id) as $sell_payment)
                                                                        @if($country == 'any')
                                                                            <li>
                                                                                <span><img width="25" src="{{$sell_payment->payment_method->image}}" alt=""></span>
                                                                                {{ $sell_payment->payment_method->name}}
                                                                            </li>
                                                                        @elseif(is_accept_payment_method($sell_payment->payment_method_id,$country))
                                                                            <li>
                                                                                <span><img width="25" src="{{$sell_payment->payment_method->image}}" alt=""></span>
                                                                                {{ $sell_payment->payment_method->name}}
                                                                            </li>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                                @endif
                                                        </td>
                                                        <td>
                                                            <h4>{{countrylist($sell->country)}}</h4>
                                                            <p>{{$sell->address}}</p>

                                                        </td>
                                                        <td>
                                                            <h4>{{number_format($sell->minimum_trade_size,2). ' '.$sell->currency }} {{__(' to ')}} {{number_format($sell->maximum_trade_size,2). ' '.$sell->currency }}</h4>
                                                        </td>
                                                        <td>
                                                            @if($sell->rate_type == RATE_TYPE_DYNAMIC)
                                                                <p class="normal">{{number_format($sell->coin_rate,2).' '.$sell->currency}}</p>
                                                                <p class="mute"> {{number_format($sell->rate_percentage,2)}} % {{price_rate_type($sell->price_type)}} {{__(' Market')}}</p>

                                                            @else
                                                                <p class="normal">{{number_format($sell->coin_rate,2).' '.$sell->currency}}</p>
                                                                <p class="mute">  {{__(' Static Rate')}}</p>
                                                            @endif
                                                        </td>
                                                        <td class="text-right">
                                                            <a href="{{route('openTrade',['buy',$sell->id])}}"><button class="btn theme-btn">{{__('Buy Now')}}</button></a>
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
                                            <h4 id="list_title">{{__('Sell ')}} {{check_default_coin_type($coins_type)}} {{__(' to these buyers')}}</h4>
                                        </div>
                                    </div>
                                    <div class="cp-user-wallet-table table-responsive buy-table crypto-exchange-table">
                                        <table class="table">
                                            <tbody>
                                            @if(isset($buys[0]))
                                                @foreach($buys as $buy)
                                                    <tr>
                                                        <td>
                                                            <h4><a href="{{route('userTradeProfile',$buy->user_id)}}">{{$buy->user->first_name.' '.$buy->user->last_name}}</a></h4>
                                                            <p> {{count_trades($buy->user->id)}} {{__(' trades')}}</p>
                                                        </td>
                                                        <td>
                                                            <h4>{{__('Payment System')}}</h4>
                                                                @if(isset($buy->payment($buy->id)[0]))
                                                                    <ul class="payment-system-list">
                                                                        @foreach($buy->payment($buy->id) as $buy_payment)
                                                                            @if($country == 'any')
                                                                                <li>
                                                                                    <span><img width="25" src="{{$buy_payment->payment_method->image}}" alt=""></span>{{ $buy_payment->payment_method->name}}
                                                                                </li>
                                                                            @elseif(is_accept_payment_method($buy_payment->payment_method_id,$country))
                                                                                <li>
                                                                                    <span><img width="25" src="{{$buy_payment->payment_method->image}}" alt=""></span>{{ $buy_payment->payment_method->name}}
                                                                                </li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                        </td>
                                                        <td><h4>{{countrylist($buy->country)}}</h4></td>
                                                        <td>
                                                            {{number_format($buy->minimum_trade_size,2). ' '.$buy->currency }} {{__(' to ')}} {{number_format($buy->maximum_trade_size,2). ' '.$buy->currency }}
                                                        </td>
                                                        <td>
                                                            @if($buy->rate_type == RATE_TYPE_DYNAMIC)
                                                                <p class="normal">{{number_format($buy->coin_rate,2).' '.$buy->currency}}</p>
                                                                <p class="mute"> {{number_format($buy->rate_percentage,2)}} % {{price_rate_type($buy->price_type)}} {{__(' Market')}}</p>

                                                            @else
                                                                <p class="normal">{{number_format($buy->coin_rate,2).' '.$buy->currency}}</p>
                                                                <p class="mute">  {{__(' Static Rate')}}</p>
                                                            @endif
                                                        </td>
                                                        <td class="text-right">
                                                            <a href="{{route('openTrade',['sell',$buy->id])}}"><button class="btn theme-btn">{{__('Sell Now')}}</button></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="7" class="text-center">{{__('No data available')}}</td>
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
        </div>
    </div>
    {{-- popup --}}
    <div class="modal fade " id="popUpModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-content">
                                <p>
                                    @if(isset($settings['terms_condition']))
                                        {!! $settings['terms_condition'] !!}
                                    @else
                                        "Lorem ipsum dolor sit amet, consectetur adipisicing elit. A animi distinctio dolore
                                        ex, harum illum in inventore ipsam iusto laborum maiores minima minus modi nulla
                                        odio odit pariatur porro quod ratione reiciendis rerum tempore velit veritatis!
                                        Consequuntur corporis dolores ea eaque, error excepturi id ipsam iste magnam
                                        mollitia non nulla obcaecati placeat possimus provident quibusdam quod quos tempora.
                                        A autem cumque cupiditate, debitis distinctio, esse harum labore nobis nulla
                                        perferendis quasi tempora voluptas? Consequatur corporis cum reiciendis! A
                                        accusantium aperiam aspernatur at autem debitis delectus dolores error est eum
                                        eveniet ex facilis id itaque, laudantium magnam minima natus neque nihil nisi nobis
                                        numquam officiis perferendis quia quidem quisquam, repellendus reprehenderit saepe
                                        tempora ullam? Esse eum illum, neque obcaecati officiis quae repudiandae sequi. Aut
                                        corporis debitis dignissimos error, est fugit iste laboriosam laudantium maiores nam
                                        nobis quae quidem quod repellat repellendus rerum similique soluta voluptatem
                                        voluptates voluptatibus. Consequatur dolorem eos id illo numquam odit perspiciatis
                                        recusandae repudiandae suscipit unde. Aliquam, amet aperiam consequuntur corporis
                                        cum delectus dignissimos, ex, molestias obcaecati placeat quam quisquam quos rerum
                                        temporibus ut veritatis voluptates? Dolore facilis illum impedit natus quae?
                                        Accusantium adipisci asperiores atque cum delectus esse, eum ex illo in incidunt
                                        minima molestiae natus nemo neque nisi odio officiis possimus provident quae quaerat
                                        quas reiciendis rem repudiandae sint voluptas voluptate, voluptatem. Aspernatur,
                                        harum mollitia necessitatibus porro sit suscipit unde? Animi deleniti ducimus ea,
                                        eius harum ipsum magnam nesciunt officiis reiciendis vel! Aliquid atque deserunt
                                        fugit laborum qui quisquam saepe sapiente sequi vero voluptate. Aliquam aliquid
                                        consequatur culpa in incidunt odio quibusdam, temporibus voluptatibus. Alias aliquid
                                        commodi labore placeat tempora! Ad aperiam consequuntur, deserunt dolore eligendi
                                        magnam nisi pariatur quam quidem, repellat reprehenderit saepe sequi vero. Culpa
                                        debitis et minus modi nesciunt quidem, rem saepe? Accusantium at cumque enim
                                        exercitationem, optio pariatur provident quo veniam! Accusamus alias aliquam amet
                                        blanditiis consectetur cupiditate delectus deleniti deserunt distinctio dolor eaque
                                        esse excepturi exercitationem expedita illo inventore iure labore laudantium minus,
                                        necessitatibus non obcaecati odit optio pariatur perferendis possimus quae quidem
                                        quos repudiandae saepe sapiente sequi, sit, temporibus tenetur unde vel voluptatem.
                                        Aliquam autem consequuntur ducimus, earum eum eveniet excepturi inventore itaque
                                        labore laboriosam laborum necessitatibus nulla numquam porro quas qui ratione sed
                                        totam ut voluptatum? Corporis nostrum recusandae voluptas. Corporis iste quia rerum.
                                        Amet at autem beatae blanditiis consequatur culpa delectus dolor earum eligendi est
                                        exercitationem facere fuga fugit hic illo illum, ipsum iure modi molestiae,
                                        necessitatibus neque numquam porro praesentium quas quia quibusdam quos recusandae
                                        sit voluptatem voluptates? Accusamus accusantium alias architecto aspernatur
                                        assumenda at consectetur consequatur culpa cumque dolor earum eius eligendi enim
                                        error est ex illo incidunt maxime minus molestias nisi nulla quia quibusdam quos
                                        repellendus vel vero, voluptas! Animi aut blanditiis consequatur cumque cupiditate
                                        dicta dolor dolore dolores eaque earum excepturi facere, facilis hic impedit in ipsa
                                        ipsum iusto labore libero magnam modi necessitatibus nesciunt, nobis officia,
                                        placeat possimus quae quam qui quos repudiandae saepe totam voluptate voluptatum.
                                        Ratione, unde, vel! Aperiam aspernatur commodi ea et ex libero neque pariatur
                                        provident! Dolorem doloribus, minus."
                                    @endif
                                </p>
                            </div>
                            <div class="border-top pt-4">
                                <p>Please read this terms and condition carefully. It is necessary that
                                    you
                                    read and understand the information</p>
                            </div>
                            <form class="mt-4" action="{{route('saveUserAgreement')}}" method="POST">
                                @csrf
                                <div class="form-check">
                                    <input class="form-check-input d-none" type="radio" name="agree_terms"
                                           id="popupRadio1"
                                           value="{{AGREE}}">
                                    <label class="form-check-label" for="popupRadio1">{{__('Understand and Agree')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input d-none" type="radio" name="agree_terms"
                                           id="popupRadio2"
                                           value="{{NOT_AGREE}}">
                                    <label class="form-check-label" for="popupRadio2">{{__('Not agree')}}</label>
                                </div>
                                <div class="form-group mt-4">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('Close')}}</button>
                                    <button type="submit" class="btn theme-btn">{{__('Continue')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#country').on('change', function () {
            var country_id = $(this).val();

            $.ajax({
                type: "POST",
                url: "{{route('getCountryPaymentMethod')}}",
                data: {
                    '_token': "{{csrf_token()}}",
                    'country': country_id
                },
                dataType: 'JSON',

                success: function (data) {
                    console.log(data)
                    $('#select-payment-method').html("<option value=\"any\">{{__('Any Payment Method')}}</option>" + data.data)
                    $('#select-payment-method').selectpicker('refresh');
                }
            })
        });

        @if(Auth::user() && Auth::user()->agree_terms == STATUS_DEACTIVE)

        $(window).on('load', function () {
            $('#popUpModal').modal('show');
        });

        @endif

    </script>
@endsection
