<?php
namespace App\Repository;
use App\Http\Services\CommonService;
use App\Model\Bank;
use App\Model\Chat;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ChatRepository
{
    // user message list

    public function messageList($sender_id, $order_id)
    {
        try {
            $response['chat_list'] = [];

//            $messages = Chat::where('order_id', $order_id)->where(function ($query) use ($sender_id) {
//                $query->where('receiver_id', '=', Auth::user()->id)
//                    ->where('sender_id', '=', $sender_id);
//            })->orWhere(function ($query) use ($sender_id) {
//                $query->where('receiver_id', '=', $sender_id)
//                    ->where('sender_id', '=', Auth::user()->id);
//            })->get();

            $messages = Chat::where('order_id', $order_id)->get();
            if (isset($messages[0])) {
                $response = [
                    'success' => true,
                    'chat_list' => $messages
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => __('No message available'),
                    'chat_list' => []
                ];
            }

        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'chat_list' => []
//                'message' => __('Something Went wrong !')
            ];
            return $response;
        }


        return $response;
    }

    // message save process
    public function sendOrderMessage($request)
    {
        try {
            if (empty($request->message)) {
                $response = [
                    'success' => false,
                    'message' => __('You can not send empty message')
                ];
                return $response;
            }
            $data = [
                'sender_id' => Auth::user()->id,
                'message' => $request->message,
                'order_id' => $request->order_id,
                'receiver_id' => decrypt($request->receiver_id),
            ];

            $saveData = Chat::create($data);
            if ($saveData) {
                $heading = Auth::user()->first_name.' '.Auth::user()->last_name.' messaged you.';
                app(CommonService::class)->sendNotificationToUser($saveData->receiver_id,$heading,$saveData->message);
                $response = [
                    'success' => true,
                    'message' => __('New message send successfully'),
                    'data' => $saveData
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => __('Failed to send')
                ];
            }

        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
//                'message' => __('Something Went wrong !')
            ];
            return $response;
        }

        return $response;
    }


}
