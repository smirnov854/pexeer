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
                                            @include('user.marketplace.market.order.leftside')
                                        </div>
                                        <div class="col-xl-7 rightSideDiv">
                                            @include('user.marketplace.market.order.rightside')
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
                    {{--let audio = new Audio('{{asset('assets/chin-up.mp3')}}');--}}
                    {{--audio.play();--}}

                    $('.inner-message').scrollTop($('.inner-message')[0].scrollHeight);
                });

            $('form#idForm').on('submit', function (e) {
                e.preventDefault();

                var receiverId = $('#receiverId').val();
                var textMessage = $('#textMessage').val();

                sendMessage(receiverId, textMessage);
                $("#idForm")[0].reset();
            });

            $('.inner-message').scrollTop($('.inner-message')[0].scrollHeight);
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

                        $("#messageData").append('<li class="message-sent single-message">' +
                            '<div class="msg">' + '<p>' + message + '</p>' + '</div>' +
                            '<div class="user-img"> <img src="' + myImage + '"> </div></li>');
                        $('.inner-message').scrollTop($('.inner-message')[0].scrollHeight);
                    }
                });
            }

            Echo.channel('sendorderstatus_'+'{{Auth::id()}}'+'_'+'{{$item->id}}')
                .listen('.receive_order_status', (data) => {
                    $('.rightSideDiv').html(data.html);
                });

        });
    </script>
@endsection
