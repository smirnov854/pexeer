@extends('user.master',[ 'menu'=>'trade'])
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
                                            <h4 id="list_title2"><a href="{{route('marketPlace')}}">{{__(' My Trades ')}}</a> -> {{$type_text}}
                                                @if($type == 'seller')
                                                    <a href="{{route('userTradeProfile',$item->buyer_id)}}">{{$item->buyer->first_name.' '.$item->buyer->last_name}}</a>
                                                @else
                                                    <a href="{{route('userTradeProfile',$item->seller_id)}}">{{$item->seller->first_name.' '.$item->seller->last_name}}</a>
                                                @endif
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-5">
                                            <div class="cp-user-card-header-area">
                                                <div class="title">
                                                    <h4 id="list_title2">{{__('Conversation')}}</h4>
                                                    <h4 id="list_title2">{{__('Messages are end-to-end encrypted.')}}</h4>
                                                </div>
                                            </div>
                                                <p class="mt-2">
                                                    <span class="">{{__('Say hello and exchange payment details with the other user. ')}}</span>
                                                    <span class="text-warning"><b>{{__(' Remember:')}}</b></span>
                                                    <span>
                                                        <ul>
                                                            @if($type == 'seller')
                                                            <li>{{__('Escrow should be released on the spot during the in-person exchange.')}}</li>
                                                            @else
                                                                <li>{{__('Escrow should be released on the spot during the in-person exchange. Don\'t leave until the escrow is released.')}}</li>
                                                            @endif
                                                            <li>{{__('Always use escrow. It\'s there for your safety.')}}</li>
                                                            <li>{{__('Open a payment dispute if you run into trouble.')}}</li>
                                                        </ul>
                                                    </span>
                                                </p>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="messages-box-right">
                                                        @if(isset($selected_user))
                                                            <div class="online-active">
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="user-area">
                                                                            <div class="user-img">
                                                                                <a href="#"><img src="{{ show_image($selected_user->id,'user') }}"  alt="">
                                                                                </a>
                                                                            </div>
                                                                            <div class="user-name">
                                                                                <h5><a href="#">{{ $selected_user->first_name.' '.$selected_user->last_name  }}</a></h5>
                                                                                @if($selected_user->isOnline()) <span>{{__('Active Now')}}</span> @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="messages-box">
                                                            <div class="inner-message">
                                                                @if(!empty($chat_list))
                                                                    <ul id="messageData">
                                                                        @foreach($chat_list as $text)
                                                                            <li class="{{ ($text->sender_id == Auth::user()->id) ? 'message-sent' : 'messages-resived' }} single-message">
                                                                                @if($text->sender_id == Auth::user()->id)
                                                                                    <div class="msg">
                                                                                        <p>{{ $text->message }} </p>
                                                                                    </div>
                                                                                @endif
                                                                                <div class="user-img">
                                                                                    <img
                                                                                        @if(isset($text->sender_id))
                                                                                        src="{{show_image($text->sender_id,'user')}}"
                                                                                        @endif

                                                                                        @if(isset($text->receiver_id))
                                                                                        src="{{show_image($text->receiver_id,'user')}}"
                                                                                        @endif  alt="">
                                                                                </div>
                                                                                @if($text->receiver_id == Auth::user()->id)
                                                                                    <div class="msg">
                                                                                        <p>{{ $text->message }}  </p>
                                                                                    </div>
                                                                                @endif
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if(isset($selected_user))
                                                            <div class="text-messages-area">
                                                                <Form id ="idForm">
                                                                    <div class="alert alert-danger myalert" id="notification_box" style="display:none"></div>
                                                                    <div class="text-messages-inner">
                                                                        <div class="form-group">
                                                                            <input type="hidden" id="receiverId" name="receiver_id" @if(isset($selected_user)) value="{{encrypt($selected_user->id)}}" @endif>
                                                                            <textarea required name="message" id="textMessage">{{old('message')}}</textarea>
                                                                        </div>
                                                                        <div class="send-btn">
                                                                            <button type="submit" id="submitButton" class="send">{{__('Send')}}</button>
                                                                        </div>
                                                                    </div>
                                                                </Form>
                                                            </div>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-7">
                                            <div class="row align-items-end">
                                                <div class="col-md-6">
                                                    <div class="cp-user-card-header-area">
                                                        <div class="title">
                                                            <h4 id="list_title2">{{__('You are the ')}}{{$type == 'buyer' ? __('Buyer') : __('Seller')}}</h4>
                                                            <h4 id="list_title2">{{__('Order id :  ')}} {{ $item->order_id}}</h4>
                                                            @if($item->status == TRADE_STATUS_TRANSFER_DONE)
                                                                <h4 id="list_title2">{{__('Transaction id :  ')}} {{ $item->transaction_id}}</h4>
                                                            @endif
                                                            <h4 id="list_title2">{{__('Selected payment method is :  ')}}
                                                                <span><img src="{{$item->payment_method->image}}" alt=""></span>
                                                                {{ $item->payment_method->name}}
                                                            </h4>
                                                            @if($item->status != TRADE_STATUS_TRANSFER_DONE)
                                                                <h4 id="list_title2">{{__('Waiting for the ')}}{{$type == 'seller' ? __('Buyer') : __('Seller')}}</h4>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    @if(!empty($item->payment_sleep))
                                                        <div class="view-payment-receipt">
                                                            <Button class="btn theme-btn" data-target="#view-payment-receipt" data-toggle="modal">{{__('View Payment Receipt')}}</Button>
                                                        </div>
                                                        <div id="view-payment-receipt" class="modal fade view-payment-receipt-modal" role="dialog">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header"><h6 class="modal-title">{{__('Payment Receipt')}}</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>

                                                                    <div class="modal-body">
                                                                        @if(!empty($item->payment_sleep))
                                                                            <img src="{{asset(path_image().$item->payment_sleep)}}" alt="">
                                                                        @else
                                                                            <p>{{__('Payment slip not found')}}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="alert alert-info" role="alert">
                                                @if($type == 'buyer')
                                                    {{__('Don’t pay the seller until they put ')}} {{check_default_coin_type($item->coin_type)}} {{__(' in escrow. Once escrowed, this status will change.')}}
                                                @else
                                                    {{__('Before accepting payment, you must put the ')}} {{check_default_coin_type($item->coin_type)}} {{__(' into a secure escrow account.')}}
                                                @endif
                                            </div>

                                            <div class="row no-gutters">
                                                @if($item->status == TRADE_STATUS_CANCEL)
                                                    <div class="col-md-12">
                                                        <p class =" text-center font-weight-bold text-danger">
                                                            {{__('Order Cancelled')}}
                                                        </p>
                                                    </div>
                                                @else
                                                    <div class="col-md-4 col-lg-3">
                                                        <p class="trade-step step-complete">
                                                            @if(!empty($item->buy_id))
                                                                {{__('Sell order placed')}}
                                                            @elseif(!empty($item->sell_id))
                                                                {{__('Buy order placed')}}
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-md-4 col-lg-3">
                                                        <p class ="trade-step @if($item->status == TRADE_STATUS_ESCROW || $item->status == TRADE_STATUS_PAYMENT_DONE || $item->status == TRADE_STATUS_TRANSFER_DONE) step-complete @endif">
                                                            {{__('Seller puts ')}} {{check_default_coin_type($item->coin_type)}} {{__(' in escrow')}}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-4 col-lg-3">
                                                        <p class ="trade-step @if($item->status == TRADE_STATUS_PAYMENT_DONE || $item->status == TRADE_STATUS_TRANSFER_DONE) step-complete @endif">
                                                            {{__('Buyer pays seller directly')}}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-4 col-lg-3">
                                                        <p class ="trade-step @if($item->status == TRADE_STATUS_TRANSFER_DONE) step-complete @endif">
                                                            {{__('Escrow released to buyer')}}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="cp-user-card-header-area">
                                                        <div class="title">
                                                            <h4 id="list_title2">{{$type == 'seller' ? __('Selling') : __('Buying')}}</h4>
                                                            <h4 id="list_title2">{{number_format(($item->amount),8).' '.check_default_coin_type($item->coin_type) }}</h4>
                                                            <h4 id="list_title2">{{__('for')}}</h4>
                                                            <h4 id="list_title2">{{ $item->currency .' '.number_format($item->price,8) }}</h4>
                                                            <h4 id="list_title2">1 {{ check_default_coin_type($item->coin_type) .' = '.$item->currency .' '.number_format($item->rate,8) }}</h4>
                                                            <h4 id="list_title2"> {{ __('Fees') .' = '.number_format($item->fees,8).' '.check_default_coin_type($item->coin_type) }}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    @if($item->status == TRADE_STATUS_TRANSFER_DONE)
                                                        <div class="cp-user-card-header-area">
                                                            <div class="title">
                                                                <h4 id="list_title2" class="text-success">{{__('Transaction Successful')}}</h4>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($item->is_reported == STATUS_ACTIVE)
                                                        @if(isset($report))
                                                            <div class="cp-user-card-header-area">
                                                                <div class="title">
                                                                    <h4 id="list_title2" class="text-danger">
                                                                        @if($report->type == BUYER) {{__('The buyer ')}} @else {{__('The seller ')}} @endif {{__(' reported against order.')}}
                                                                    </h4>
                                                                    @if(!empty($item->transaction_id))
                                                                        <h5 class="text-warning">{{ $item->transaction_id }}</h5>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <ul class="d-flex">
                                                    @if($type == 'seller')
                                                        @if($item->status == TRADE_STATUS_INTERESTED)
                                                            <li class="deleteuser mr-3">
                                                                <a title="{{__('Fund Escrow')}}" href="#escrow_{{($item->id)}}" data-toggle="modal">
                                                                    <button class="btn theme-btn">{{__('Fund Escrow')}}</button>
                                                                </a>
                                                            </li>
                                                            <div id="escrow_{{($item->id)}}" class="modal fade delete" role="dialog">
                                                                <div class="modal-dialog modal-md">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header"><h6 class="modal-title">{{__('Escrow')}}</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                                                        @if(isset($check_balance))
                                                                        <div class="modal-body">
                                                                            <p>{{__('Do you want to escrow ?')}}</p>
                                                                            @if($check_balance['success'] == false)
                                                                                <p class="text-danger">{{ $check_balance['message'] }}</p>
                                                                            @endif
                                                                        </div>
                                                                        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">{{__("Close")}}</button>
                                                                            @if($check_balance['success'] == true)
                                                                                <a class="btn btn-danger" href="{{route('fundEscrow', encrypt($item->id))}}">{{__('Confirm')}}</a>
                                                                            @endif
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if($item->status == TRADE_STATUS_PAYMENT_DONE)
                                                            <li class="deleteuser mr-3">
                                                                <a title="{{__('Release Escrow')}}" href="#escrow_{{($item->id)}}" data-toggle="modal">
                                                                    <button class="btn theme-btn">{{__('Release Escrow')}}</button>
                                                                </a>
                                                            </li>
                                                            <div id="escrow_{{($item->id)}}" class="modal fade delete" role="dialog">
                                                                <div class="modal-dialog modal-md">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header"><h6 class="modal-title">{{__('Release Escrow')}}</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                                                        <div class="modal-body"><p>{{__('Do you want to release escrow ?')}}</p></div>
                                                                        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">{{__("Close")}}</button>
                                                                            <a class="btn btn-danger" href="{{route('releasedEscrow', encrypt($item->id))}}">{{__('Confirm')}}</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if($item->status == TRADE_STATUS_INTERESTED || $item->status == TRADE_STATUS_ESCROW)
                                                            <li class="deleteuser mr-3">
                                                                <a title="{{__('Cancel Trade')}}" href="#cancel_{{($item->id)}}" data-toggle="modal">
                                                                    <button class="btn theme-btn">{{__('Cancel Trade')}}</button>
                                                                </a>
                                                            </li>
                                                            <div id="cancel_{{($item->id)}}" class="modal fade delete" role="dialog">
                                                                <div class="modal-dialog modal-md">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header"><h6 class="modal-title">{{__('Cancel')}}</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                                                        <div class="modal-body">
                                                                            <p>{{__('Do you want to cancel order ?')}}</p>
                                                                            <form action="{{route('tradeCancel')}}" method="POST" enctype="multipart/form-data"
                                                                                  id="">
                                                                                @csrf
                                                                                <input type="hidden" name="order_id" value="{{encrypt($item->id)}}">
                                                                                <input type="hidden" name="type" value="{{SELLER}}">
                                                                                <div class="cp-user-payment-type">
                                                                                    <h3>{{__('Reason')}} </h3>
                                                                                    <textarea name="reason" required class="form-control"></textarea>
                                                                                </div>
                                                                                <button type="submit" class=" mt-4 btn theme-btn">{{__('Submit')}}</button>
                                                                            </form>
                                                                        </div>
                                                                        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">{{__("Close")}}</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if($type == 'buyer')
                                                        @if($item->status == TRADE_STATUS_INTERESTED)
                                                            <li class="deleteuser mr-3">
                                                                <a title="{{__('Cancel Trade')}}" href="#cancel_{{($item->id)}}" data-toggle="modal">
                                                                    <button class="btn theme-btn">{{__('Cancel Trade')}}</button>
                                                                </a>
                                                            </li>
                                                            <div id="cancel_{{($item->id)}}" class="modal fade delete" role="dialog">
                                                                <div class="modal-dialog modal-md">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header"><h6 class="modal-title">{{__('Cancel')}}</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                                                        <div class="modal-body">
                                                                            <p>{{__('Do you want to cancel order ?')}}</p>
                                                                            <form action="{{route('tradeCancel')}}" method="POST" enctype="multipart/form-data"
                                                                                  id="">
                                                                                @csrf
                                                                                <input type="hidden" name="order_id" value="{{encrypt($item->id)}}">
                                                                                <input type="hidden" name="type" value="{{BUYER}}">
                                                                                <div class="cp-user-payment-type">
                                                                                    <h3>{{__('Reason')}} </h3>
                                                                                    <textarea name="reason" required class="form-control"></textarea>
                                                                                </div>
                                                                                <button type="submit" class=" mt-4 btn theme-btn">{{__('Submit')}}</button>
                                                                            </form>
                                                                        </div>
                                                                        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">{{__("Close")}}</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if($item->status == TRADE_STATUS_ESCROW)
                                                            <li class="deleteuser mr-3">
                                                                <a title="{{__('Upload Payment Slip')}}" href="#upload_{{($item->id)}}" data-toggle="modal">
                                                                    <button class="btn theme-btn">{{__('Upload Payment Slip')}}</button>
                                                                </a>
                                                            </li>
                                                            <div id="upload_{{($item->id)}}" class="modal fade delete" role="dialog">
                                                                <div class="modal-dialog modal-md">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header"><h6 class="modal-title">{{__('Upload Payment Slip')}}</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                                                        <div class="modal-body">
                                                                            <p>{{__('If your payment has done then upload your Payment Slip')}}</p>
                                                                            <form action="{{route('uploadPaymentSleep')}}" method="POST" enctype="multipart/form-data"
                                                                                  id="">
                                                                                @csrf
                                                                                <input type="hidden" name="order_id" value="{{encrypt($item->id)}}">
                                                                                <div class="cp-user-payment-type">
                                                                                    <h3>{{__('Payment Slip ')}} </h3>
                                                                                    <div id="file-upload" class="section-p">
                                                                                        <input type="file" placeholder="0.00" required name="payment_sleep" value=""
                                                                                            id="file" ref="file" class="dropify" />
                                                                                    </div>
                                                                                </div>

                                                                                <button type="submit" class=" mt-4 btn theme-btn">{{__('Upload')}}</button>
                                                                            </form>
                                                                        </div>
                                                                        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">{{__("Close")}}</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    @if($item->status != TRADE_STATUS_TRANSFER_DONE)
                                                        <li class="deleteuser mr-3">
                                                            <a title="{{__('Report User')}}" href="#report_{{($item->id)}}" data-toggle="modal">
                                                                <button class="btn theme-btn">{{__('Report User')}}</button>
                                                            </a>
                                                        </li>
                                                        <div id="report_{{($item->id)}}" class="modal fade delete" role="dialog">
                                                            <div class="modal-dialog modal-md">
                                                                <div class="modal-content">
                                                                    <div class="modal-header"><h6 class="modal-title">{{__('Report User')}}</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                                                    <div class="modal-body">
                                                                        <p>{{__('Do you want to report this order ?')}}</p>
                                                                        <form action="{{route('reportUserOrder')}}" method="POST" enctype="multipart/form-data"
                                                                              id="">
                                                                            @csrf
                                                                            <input type="hidden" name="order_id" value="{{encrypt($item->id)}}">
                                                                            <input type="hidden" name="type" @if($type =='seller') value="{{SELLER}}" @else value="{{BUYER}}" @endif>
                                                                            <div class="cp-user-payment-type">
                                                                                <h3>{{__('Reason')}} </h3>
                                                                                <textarea name="reason"  class="form-control"></textarea>
                                                                            </div>
                                                                            <div class="cp-user-payment-type mt-3">
                                                                                <h3>{{__('Attachment (if any) ')}} </h3>
                                                                                <div id="file-upload" class="section-p">
                                                                                    <input type="file" placeholder="0.00"  name="attach_file" value=""
                                                                                        id="file" ref="file" class="dropify" />
                                                                                </div>
                                                                            </div>
                                                                            <button type="submit" class=" mt-4 btn theme-btn">{{__('Submit')}}</button>
                                                                        </form>
                                                                    </div>
                                                                    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">{{__("Close")}}</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    </ul>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row mt-5">
                                                <div class="col-xl-4">
                                                    <div class="cp-user-card-header-area">
                                                        <div class="title">
                                                            <h4 id="list_title2">{{__('About the ')}}{{$type == 'seller' ? __('Buyer') : __('Seller')}}</h4>
                                                        </div>
                                                    </div>
                                                    <ul class="seller-info">
                                                        <li>
                                                            @if($type == 'seller')
                                                                <a href="{{route('userTradeProfile',$item->buyer_id)}}">{{$item->buyer->first_name.' '.$item->buyer->last_name}}</a>
                                                            @else
                                                                <a href="{{route('userTradeProfile',$item->seller_id)}}">{{$item->seller->first_name.' '.$item->seller->last_name}}</a>
                                                            @endif
                                                        </li>
                                                        <li>{{__('100% good feedback')}}</li>
                                                        <li>{{__('Registered ')}}
                                                            @if($type == 'seller')
                                                                 {{date('M Y', strtotime($item->buyer->created_at))}}
                                                            @else
                                                                {{date('M Y', strtotime($item->seller->created_at))}}
                                                            @endif
                                                        </li>
                                                        <li>
                                                            @if($type == 'seller')
                                                                {{count_trades($item->buyer_id)}} {{__(' trades')}}
                                                            @else
                                                                {{count_trades($item->seller_id)}} {{__(' trades')}}
                                                            @endif
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-xl-8">
                                                    <div class="cp-user-card-header-area">
                                                        <div class="title">
                                                            <h4 id="list_title2">{{__('Headline ')}}</h4>
                                                            @if(!empty($item->buy_id))
                                                                <b>{{ $item->buy_data->headline }}</b>
                                                            @elseif(!empty($item->sell_id))
                                                                <b>{{ $item->sell_data->headline }}</b>
                                                            @else
                                                                <p>'.....................................'</p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="cp-user-card-header-area">
                                                        <div class="title">
                                                            <h4 id="list_title2">{{__('Terms and Condition ')}}</h4>
                                                            @if(!empty($item->buy_id))
                                                                <p>{{ $item->buy_data->terms }}</p>
                                                            @elseif(!empty($item->sell_id))
                                                                <p>{{ $item->sell_data->terms }}</p>
                                                            @else
                                                                <p>'.....................................'</p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="cp-user-card-header-area">
                                                        <div class="title">
                                                            <h4 id="list_title2">{{__('Trading Instruction')}}</h4>
                                                            @if(!empty($item->buy_id))
                                                                <p>{{ $item->buy_data->instruction }}</p>
                                                            @elseif(!empty($item->sell_id))
                                                                <p>{{ $item->sell_data->instruction }}</p>
                                                            @else
                                                                <p>'.....................................'</p>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        jQuery(document).ready(function () {
            Pusher.logToConsole = true;

            Echo.channel('userordermessage_' + '{{Auth::id()}}' + '_' + '{{$item->id}}')
                .listen('.receive_message', (data) => {
                    console.log(data);
                    var message = data.data.message;
                    var image = data.data.sender_user.image;

                    $("#messageData").append('<li class="messages-resived single-message">' +
                        '<div class="user-img"> <img src="' + image + '"> </div>' +
                        '<div class="msg">' + '<p>' + message + '</p>' + '</div></li>');
                });
            $('body').keydown(function(event) {
                if(event.which == 13) {
                    console.log('enter');
                }
                console.log(event.keyCode);
            });

            $('form#idForm').on('submit', function (e) {
                e.preventDefault();

                var receiverId = $('#receiverId').val();
                var textMessage = $('#textMessage').val();

                sendMessage(receiverId, textMessage);
                $("#idForm")[0].reset();
            });

            function sendMessage(receiverId, textMessage) {
                $.ajax({
                    type: "POST",
                    url: '{{ route('sendOrderMessage') }}',
                    data: {
                        '_token': '{{csrf_token()}}',
                        'receiver_id': receiverId,
                        'message': textMessage,
                        'order_id': '{{$item->id}}',
                    },
                    success: function (data) {
                        var message = data.data.data.message;
                        var myImage = data.data.data.my_image;

                        console.log(myImage);
                        $("#messageData").append('<li class="message-sent single-message">' +
                            '<div class="msg">' + '<p>' + message + '</p>' + '</div>' +
                            '<div class="user-img"> <img src="' + myImage + '"> </div></li>');
                    }
                });
            }
        });
    </script>
@endsection
