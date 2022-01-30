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
            <li class="text-success">
                <span>{{$type == 'seller' ? get_user_feedback_rate($item->buyer_id).'%' : get_user_feedback_rate($item->seller_id).'%'}}</span>
                {{__(' good feedback')}}</li>
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
