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
