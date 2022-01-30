<?php

namespace App\Http\Controllers\user\marketplace;

use App\Http\Requests\PlaceOrderRequest;
use App\Http\Requests\ReportUserRequest;
use App\Http\Requests\TradeCancelRequest;
use App\Http\Requests\UploadSleepRequest;
use App\Http\Services\CommonService;
use App\Model\Buy;
use App\Model\Coin;
use App\Model\Order;
use App\Model\OrderDispute;
use App\Model\PaymentMethod;
use App\Model\Sell;
use App\Repository\ChatRepository;
use App\Repository\MarketRepository;
use App\Repository\OfferRepository;
use App\Services\Logger;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;
use Stevebauman\Location\Facades\Location;

class MarketplaceController extends Controller
{

    /**
     * Initialize market service
     *
     * MarketController constructor.
     */
    public $logger;
    public $marketRepo;
    public $offerRepo;

    public function __construct()
    {
        $this->logger = new Logger();
        $this->marketRepo = new MarketRepository;
        $this->offerRepo = new OfferRepository();
    }


    // user trade profile
    public function userTradeProfile($user_id)
    {
        $myIp = Location::get(request()->ip());
        if ($myIp == false) {
            if(!empty(Auth::user()->country)) {
                $data['country'] = strtoupper(Auth::user()->country);
            } else {
                return redirect()->route('userProfile',['qr'=>'eProfile-tab'])->with('dismiss',__('Please add your country before trade'));
            }
        } else {
            $data['country'] = $myIp->countryCode;
        }
        $data['buys'] = Buy::where(['status' => STATUS_ACTIVE, 'user_id' => $user_id])->orderBy('id', 'DESC')->paginate(20);
        $data['sells'] = Sell::where(['status' => STATUS_ACTIVE, 'user_id' => $user_id])->orderBy('id', 'DESC')->paginate(20);
        $data['user'] = User::find($user_id);

        return view('user.marketplace.user.profile', $data);
    }
    // offer list
    public function marketPlace(Request $request)
    {
        $data['settings'] = allsetting();
        if($request->country) {
            $data['country'] = $request->country;
        } else {
            $data['country'] = 'any';
            $myIp = Location::get(request()->ip());
            if ($myIp == false) {
                if (Auth::user()) {
                    if(!empty(Auth::user()->country)) {
                        $data['country'] = strtoupper(Auth::user()->country);
                    } else {
                        return redirect()->route('userProfile',['qr'=>'eProfile-tab'])->with('dismiss',__('Please add your country before trade'));
                    }
                } else {
                    $data['country'] = 'any';
                }
            } else {
                $data['country'] = $myIp->countryCode;
            }
        }
        if($request->coin_type) {
            $data['coins_type'] = $request->coin_type;
        } else {
            $data['coins_type'] = 'BTC';
        }
        if($request->payment_method) {
            $data['pmethod'] = $request->payment_method;
        } else {
            $data['pmethod'] = 'any';
        }

        if($request->offer_type) {
            $data['offer_type'] = $request->offer_type;
        }

        $data['title'] = __('Buy and Sell');

        $data['countries'] = countrylist();
        $data['coins'] = Coin::where('status', STATUS_ACTIVE)->orderBy('id', 'ASC')->get();
        $data['payment_methods'] = PaymentMethod::where('status', STATUS_ACTIVE)->orderBy('id','Desc')->get();

        if(empty($request->offer_type)) {
            if($data['country'] == 'any') {
                $data['buys'] = Buy::where(['status' => STATUS_ACTIVE, 'coin_type' => $data['coins_type']])->orderBy('id', 'DESC')->paginate(20);
                $data['sells'] = Sell::where(['status' => STATUS_ACTIVE, 'coin_type' => $data['coins_type']])->orderBy('id', 'DESC')->paginate(20);
            } else {
                $data['buys'] = Buy::where(['status' => STATUS_ACTIVE, 'coin_type' => $data['coins_type'], 'country' => $data['country']])->orderBy('id', 'DESC')->paginate(20);
                $data['sells'] = Sell::where(['status' => STATUS_ACTIVE, 'coin_type' => $data['coins_type'], 'country' => $data['country']])->orderBy('id', 'DESC')->paginate(20);
            }
        }

        if(isset($request->offer_type) && ($request->offer_type == BUY_SELL || $request->offer_type == SELL)) {
            if($request->country == 'any') {
                if($request->payment_method == 'any') {
                    $data['buys'] = Buy::where(['status' => STATUS_ACTIVE, 'coin_type' => $data['coins_type']])->orderBy('id', 'DESC')->paginate(20);
                } else {
                    $data['buys'] = Buy::join('offer_payment_methods', 'offer_payment_methods.offer_id', '=', 'buys.id')
                        ->where(['offer_payment_methods.offer_type' => BUY, 'offer_payment_methods.payment_method_id' => $request->payment_method])
                        ->where(['buys.status' => STATUS_ACTIVE, 'buys.coin_type' => $data['coins_type']])
                        ->orderBy('buys.id', 'DESC')
                        ->select('buys.*')
                        ->paginate(20);
                }
            } else {
                if($request->payment_method == 'any') {
                    $data['buys'] = Buy::where(['status' => STATUS_ACTIVE, 'coin_type' => $data['coins_type'], 'country' => $data['country']])->orderBy('id', 'DESC')->paginate(20);
                } else {
                    $data['buys'] = Buy::join('offer_payment_methods', 'offer_payment_methods.offer_id', '=', 'buys.id')
                        ->where(['offer_payment_methods.offer_type' => BUY, 'offer_payment_methods.payment_method_id' => $request->payment_method])
                        ->where(['buys.status' => STATUS_ACTIVE, 'buys.coin_type' => $data['coins_type'],'buys.country' => $data['country']])
                        ->orderBy('buys.id', 'DESC')
                        ->select('buys.*')
                        ->paginate(20);
                }
            }
        }

        if(isset($request->offer_type) && ($request->offer_type == BUY_SELL || $request->offer_type == BUY)) {
            if($request->country == 'any') {
                if($request->payment_method == 'any') {
                    $data['sells'] = Sell::where(['status' => STATUS_ACTIVE, 'coin_type' => $data['coins_type']])->orderBy('id', 'DESC')->paginate(20);
                } else {
                    $data['sells'] = Sell::join('offer_payment_methods', 'offer_payment_methods.offer_id', '=', 'sells.id')
                        ->where(['offer_payment_methods.offer_type' => SELL, 'offer_payment_methods.payment_method_id' => $request->payment_method])
                        ->where(['sells.status' => STATUS_ACTIVE, 'sells.coin_type' => $data['coins_type']])
                        ->orderBy('sells.id', 'DESC')
                        ->select('sells.*')
                        ->paginate(20);
                }
            } else {
                if($request->payment_method == 'any') {
                    $data['sells'] = Sell::where(['status' => STATUS_ACTIVE, 'coin_type' => $data['coins_type'], 'country' => $data['country']])->orderBy('id', 'DESC')->paginate(20);
                } else {
                    $data['sells'] = Sell::join('offer_payment_methods', 'offer_payment_methods.offer_id', '=', 'sells.id')
                        ->where(['offer_payment_methods.offer_type' => SELL, 'offer_payment_methods.payment_method_id' => $request->payment_method])
                        ->where(['sells.status' => STATUS_ACTIVE, 'sells.coin_type' => $data['coins_type']])
                        ->orderBy('sells.id', 'DESC')
                        ->select('sells.*')
                        ->paginate(20);
                }
            }
        }


        return view('user.marketplace.market.marketplace', $data);
    }

    // offer details
    public function openTrade($type, $offer_id)
    {
        $myIp = Location::get(request()->ip());
        if ($myIp == false) {
            if(!empty(Auth::user()->country)) {
                $data['country'] = strtoupper(Auth::user()->country);
            } else {
                return redirect()->route('userProfile',['qr'=>'eProfile-tab'])->with('dismiss',__('Please add your country before trade'));
            }
        } else {
            $data['country'] = $myIp->countryCode;
        }
        $checkKyc = $this->marketRepo->kycValidationCheckTrade(Auth::id());
        if ($checkKyc['success'] == false) {
            return redirect()->back()->with('dismiss', $checkKyc['message']);
        }
        $this->offerRepo->checkOfferWithDynamicRate($type,$offer_id);

        if($type == 'buy') {
            $offer = Sell::find($offer_id);
            if (isset($offer)) {
                $data['title'] = __('Buy ').$offer->coin_type.__(' from '). $offer->user->first_name.' '.$offer->user->last_name ;
                $data['offer'] = $offer;
                $data['type'] = $type;
                $data['type_text'] = __('Buy ').$offer->coin_type.__(' from ');
            } else {
                return redirect()->back()->with('dismiss', __('Offer not found'));
            }
        } elseif($type == 'sell') {
            $offer = Buy::find($offer_id);
            if (isset($offer)) {
                $data['title'] = __('Sell ').$offer->coin_type.__(' to '). $offer->user->first_name.' '.$offer->user->last_name ;
                $data['offer'] = $offer;
                $data['type'] = $type;
                $data['type_text'] = __('Sell ').$offer->coin_type.__(' to ');
            } else {
                return redirect()->back()->with('dismiss', __('Offer not found'));
            }
        } else {
            return redirect()->back()->with('dismiss', __('Offer not found'));
        }

        return view('user.marketplace.market.open_trade', $data);
    }

    // place order
    public function placeOrder(PlaceOrderRequest $request)
    {
        $response = $this->marketRepo->saveOrder($request);
        if ($response['success'] == true) {
            return redirect()->route('tradeDetails', $response['order']->order_id)->with('success', $response['message']);
        }

        return redirect()->back()->withInput()->with('dismiss', $response['message']);
    }

    // my trade  list
    public function myTradeList(Request $request)
    {
        $data['title'] = __('My Trade List');
        $data['items'] = Order::where(['buyer_id' => Auth::id()])->orWhere(['seller_id' => Auth::id()])->orderBy('id', 'DESC')->paginate(20);


        return view('user.marketplace.market.trade_list', $data);
    }

    // trade details
    public function tradeDetails($order_id)
    {
        $data['title'] = __('Order Details');
        $id = $order_id;

        $order = Order::where(['order_id' => $id])->first();
        $coin = Coin::where('type', $order->coin_type)->first();
        if (isset($order) && ($order->buyer_id == Auth::id())) {
            $sender_id = $order->seller_id;
            $data['type'] = 'buyer';
            $data['title'] = __('Buy ').check_default_coin_type($order->coin_type).__(' from '). $order->seller->first_name.' '.$order->seller->last_name ;
            $data['type_text'] = __('Buy ').check_default_coin_type($order->coin_type).__(' from ');

        } elseif (isset($order) && ($order->seller_id == Auth::id())) {
            $sender_id = $order->buyer_id;
            $data['type'] = 'seller';
            $data['title'] = __('Sell ').check_default_coin_type($order->coin_type).__(' to '). $order->buyer->first_name.' '.$order->buyer->last_name ;
            $data['type_text'] = __('Sell ').check_default_coin_type($order->coin_type).__(' to ');
            $data['check_balance'] = $this->marketRepo->check_wallet_balance_for_escrow(Auth::id(),$order, $coin);

        } else {
            return redirect()->back()->with(['dismiss' => __('Order not found.')]);
        }

        $data['item'] = $order;
        if($order->is_reported == STATUS_ACTIVE) {
            $data['report'] = OrderDispute::where('order_id', $order->id)->first();
        }
        $data['chat_list'] = app(ChatRepository::class)->messageList($sender_id, $order->id)['chat_list'];
        $data['selected_user'] = User::find($sender_id);

        return view('user.marketplace.market.order_details', $data);
    }

    // cancel trade
    public function tradeCancel(TradeCancelRequest $request)
    {
        $response = $this->marketRepo->cancelTrade($request);
        if ($response['success'] == true) {
            return redirect()->back()->with('success', $response['message']);
        }

        return redirect()->back()->withInput()->with('dismiss', $response['message']);
    }

    // report user for order
    public function reportUserOrder(ReportUserRequest $request)
    {
        $response = $this->marketRepo->reportToUserOrder($request);
        if ($response['success'] == true) {
            return redirect()->back()->with('success', $response['message']);
        }

        return redirect()->back()->withInput()->with('dismiss', $response['message']);
    }

    // fund escrow process
    public function fundEscrow($order_id)
    {
        $id = app(CommonService::class)->checkValidId($order_id);
        if (is_array($id)) {
            return redirect()->back()->with(['dismiss' => __('Data not found.')]);
        }
        $response = $this->marketRepo->escrowFundProcess($id);
        if ($response['success'] == true) {
            return redirect()->back()->with('success', $response['message']);
        }

        return redirect()->back()->withInput()->with('dismiss', $response['message']);
    }

    // fund escrow release process
    public function releasedEscrow($order_id)
    {
        $response = $this->marketRepo->escrowReleasedProcess($order_id);
        if ($response['success'] == true) {
            return redirect()->back()->with('success', $response['message']);
        }

        return redirect()->back()->withInput()->with('dismiss', $response['message']);
    }

    // upload payment sleep
    public function uploadPaymentSleep(UploadSleepRequest $request)
    {
        $id = app(CommonService::class)->checkValidId($request->order_id);
        if (is_array($id)) {
            return redirect()->back()->with(['dismiss' => __('Order not found.')]);
        }
        $response = $this->marketRepo->uploadPaymentSleepProcess($request);
        if ($response['success'] == true) {
            return redirect()->back()->with('success', $response['message']);
        }

        return redirect()->back()->withInput()->with('dismiss', $response['message']);
    }

    // get trade coin rate
    public function getTradeCoinRate(Request $request)
    {
        if ($request->order_type == 'sell') {
            $offer = Buy::where('id', $request->offer_id)->first();
        } else {
            $offer = Sell::where('id', $request->offer_id)->first();
        }
        $data['offer'] = $offer;
        $data['offer_type'] = $request->order_type;
        Log::info(json_encode($offer));
        if ($request->type == 'reverse') {
            $data['type'] = $request->type;
            $amount = get_offer_rate($request->amount,$offer->currency,$offer->coin_type,$offer,'reverse');
            $data['amount'] = $amount;
            $data['price'] = $request->amount;

        } else {
            $data['type'] = $request->type;
            $amount = get_offer_rate($request->amount,$offer->currency,$offer->coin_type,$offer,'same');
            $data['price'] = $amount;
            $data['amount'] = $request->amount;
        }

        return response()->json($data);

    }


    /**
     * sendUserMessage
     *
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function sendOrderMessage(Request $request) {

        $response = app(ChatRepository::class)->sendOrderMessage($request);
        $response['data']['sender_user']->image = show_image($response['data']['sender_id'],'user');
        $response['data']['my_image'] =show_image(Auth::id(),'user');
        try {
            $channel = 'userordermessage_'.$response['data']->receiver_id.'_'.$request->order_id;
            $config = config('broadcasting.connections.pusher');
            $pusher = new Pusher($config['key'], $config['secret'], $config['app_id'], $config['options']);

            $test =  $pusher->trigger($channel , 'receive_message', $response);
        } catch (\Exception $exception) {

        }

        return response()->json(['data' => $response]);

    }

    // save user agreement
    public function saveUserAgreement(Request $request)
    {
        try {
            if(isset($request->agree_terms)) {
                User::where('id',Auth::id())->update(['agree_terms' => $request->agree_terms]);
                return redirect()->back();
            } else {
                return redirect()->back()->with('dismiss', __('Your answer is required to continue.'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('dismiss', __('Something went wrong'));
        }

    }

    // update feedback
    public function updateFeedback(Request $request)
    {
        try {
            $response = $this->marketRepo->orderFeedbackUpdate($request,Auth::id());
            if ($response['success'] == true) {
                return redirect()->back()->with('success', $response['message']);
            } else {
                return redirect()->back()->with('dismiss', $response['message']);
            }
        } catch (\Exception $e) {
            $this->logger->log('updateFeedback', $e->getMessage());
            return redirect()->back()->with('dismiss', __('Something went wrong'));
        }
    }

}
