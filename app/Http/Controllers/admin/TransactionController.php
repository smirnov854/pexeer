<?php

namespace App\Http\Controllers\admin;

use App\Http\Services\TransactionService;
use App\Jobs\DistributeWithdrawalReferralBonus;
use App\Jobs\MailSend;
use App\Model\AdminReceiveTokenTransactionHistory;
use App\Model\DepositeTransaction;
use App\Model\EstimateGasFeesTransactionHistory;
use App\Model\Wallet;
use App\Model\WalletAddressHistory;
use App\Model\WithdrawHistory;
use App\Repository\AffiliateRepository;
use App\Services\CoinPaymentsAPI;
use App\Services\ERC20TokenApi;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use function foo\func;

class TransactionController extends Controller
{
    // all wallet list
    public function adminWalletList(Request $request)
    {
        $data['title'] = __('Wallet List');
        if($request->ajax()){
            $data['wallets'] = Wallet::join('users','users.id','=','wallets.user_id')
                ->join('coins', 'coins.id', '=', 'wallets.coin_id')
                ->where(['coins.status' => STATUS_ACTIVE])
                ->select(
                    'wallets.name'
                    ,'wallets.coin_type'
                    ,'wallets.balance'
                    ,'wallets.referral_balance'
                    ,'wallets.created_at'
                    ,'users.first_name'
                    ,'users.last_name'
                    ,'users.email'
                );

            return datatables()->of($data['wallets'])
                ->addColumn('user_name',function ($item){return $item->first_name.' '.$item->last_name;})
                ->addColumn('coin_type', function ($item){return check_default_coin_type($item->coin_type);})
                ->make(true);
        }

        return view('admin.wallet.index',$data);
    }

    // transaction  history
    public function adminTransactionHistory(Request $request)
    {
        $data['title'] = __('Transaction History');
        if ($request->ajax()) {
            $deposit = DepositeTransaction::select('deposite_transactions.address'
                , 'deposite_transactions.amount'
                , 'deposite_transactions.fees'
                , 'deposite_transactions.transaction_id'
                , 'deposite_transactions.confirmations'
                , 'deposite_transactions.address_type as addr_type'
                , 'deposite_transactions.created_at'
                , 'deposite_transactions.sender_wallet_id'
                , 'deposite_transactions.receiver_wallet_id'
                , 'deposite_transactions.status'
                , 'deposite_transactions.type'
            );

            return datatables()->of($deposit)
                ->addColumn('address_type', function ($dpst) {
                    if ($dpst->addr_type == 'internal_address') {
                        return 'External';
                    } else {
                        return addressType($dpst->addr_type);
                    }

                })
                ->addColumn('status', function ($dpst) {
                    return deposit_status($dpst->status);
                })
                ->addColumn('type', function ($dpst) {
                    return check_default_coin_type($dpst->type);
                })
                ->addColumn('sender', function ($dpst) {
                    return isset($dpst->senderWallet->user) ? $dpst->senderWallet->user->first_name . ' ' . $dpst->senderWallet->user->last_name : '';
                })
                ->addColumn('receiver', function ($dpst) {
                    return isset($dpst->receiverWallet->user) ? $dpst->receiverWallet->user->first_name . ' ' . $dpst->receiverWallet->user->last_name : '';
                })
                ->make(true);
        }

        return view('admin.transaction.all-transaction', $data);
    }

    // withdrawal history
    public function adminWithdrawalHistory(Request $request)
    {
        if ($request->ajax()) {
            $withdrawal = WithdrawHistory::select('withdraw_histories.address'
                    , 'withdraw_histories.amount'
                    , 'withdraw_histories.fees'
                    , 'withdraw_histories.transaction_hash'
                    , 'withdraw_histories.confirmations'
                    , 'withdraw_histories.address_type as addr_type'
                    , 'withdraw_histories.created_at'
                    , 'withdraw_histories.wallet_id'
                    , 'withdraw_histories.status'
                    , 'withdraw_histories.receiver_wallet_id'
                    , 'withdraw_histories.coin_type'
                )->where('status', STATUS_SUCCESS);
            return datatables()->of($withdrawal)
                ->addColumn('address_type', function ($dpst) {
                    return addressType($dpst->addr_type);
                })
                ->addColumn('status', function ($dpst) {
                    return deposit_status($dpst->status);
                })
                ->addColumn('coin_type', function ($dpst) {
                    return check_default_coin_type($dpst->coin_type);
                })
                ->addColumn('sender', function ($wdrl) {
                    return isset($wdrl->senderWallet->user) ? $wdrl->senderWallet->user->first_name . ' ' . $wdrl->senderWallet->user->last_name : '';
                })
                ->addColumn('receiver', function ($wdrl) {
                    return isset($wdrl->receiverWallet->user) ? $wdrl->receiverWallet->user->first_name . ' ' . $wdrl->receiverWallet->user->last_name : '';
                })
                ->make(true);
        }

        return view('admin.transaction.all-transaction');
    }


    // pending withdrawal list
    public function adminPendingWithdrawal(Request $request)
    {
        $data['title'] = __('Withdrawal');
        if ($request->ajax()) {
            $withdrawal = WithdrawHistory::select(
                'withdraw_histories.id',
                'withdraw_histories.address'
                , 'withdraw_histories.amount'
                , 'withdraw_histories.fees'
                , 'withdraw_histories.transaction_hash'
                , 'withdraw_histories.confirmations'
                , 'withdraw_histories.address_type as addr_type'
                , 'withdraw_histories.updated_at'
                , 'withdraw_histories.wallet_id'
                , 'withdraw_histories.receiver_wallet_id'
                , 'withdraw_histories.coin_type'
            )->where(['withdraw_histories.status' => STATUS_PENDING]);

            return datatables()->of($withdrawal)
                ->addColumn('address_type', function ($wdrl) {
                    return addressType($wdrl->addr_type);
                })
                ->addColumn('coin_type', function ($wdrl) {
                    return check_default_coin_type($wdrl->coin_type);
                })
                ->addColumn('sender', function ($wdrl) {
                    return isset($wdrl->senderWallet->user) ? $wdrl->senderWallet->user->first_name . ' ' . $wdrl->senderWallet->user->last_name : '';
                })
                ->addColumn('receiver', function ($wdrl) {
                    return isset($wdrl->receiverWallet->user) ? $wdrl->receiverWallet->user->first_name . ' ' . $wdrl->receiverWallet->user->last_name : '';
                })
                ->addColumn('actions', function ($wdrl) {
                    $action = '<ul>';
                    $action .= accept_html('adminAcceptPendingWithdrawal',encrypt($wdrl->id));
                    $action .= reject_html('adminRejectPendingWithdrawal',encrypt($wdrl->id));
                    $action .= '<ul>';

                    return $action;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        return view('admin.transaction.pending-withdrawal', $data);
    }

    // rejected withdrawal list
    public function adminRejectedWithdrawal(Request $request)
    {
        $data['title'] = __('Rejected Withdrawal');
        if ($request->ajax()) {
            $withdrawal = WithdrawHistory::select(
                'withdraw_histories.address'
                , 'withdraw_histories.amount'
                , 'withdraw_histories.fees'
                , 'withdraw_histories.transaction_hash'
                , 'withdraw_histories.confirmations'
                , 'withdraw_histories.address_type as addr_type'
                , 'withdraw_histories.updated_at'
                , 'withdraw_histories.wallet_id'
                , 'withdraw_histories.receiver_wallet_id'
                , 'withdraw_histories.coin_type'
            )->where(['withdraw_histories.status' => STATUS_REJECTED]);

            return datatables()->of($withdrawal)
                ->addColumn('address_type', function ($wdrl) {
                    return addressType($wdrl->addr_type);
                })
                ->addColumn('coin_type', function ($wdrl) {
                    return check_default_coin_type($wdrl->coin_type);
                })
                ->addColumn('sender', function ($wdrl) {
                    return isset($wdrl->senderWallet->user) ? $wdrl->senderWallet->user->first_name . ' ' . $wdrl->senderWallet->user->last_name : '';
                })
                ->addColumn('receiver', function ($wdrl) {
                    return isset($wdrl->receiverWallet->user) ? $wdrl->receiverWallet->user->first_name . ' ' . $wdrl->receiverWallet->user->last_name : '';
                })
                ->make(true);
        }

        return view('admin.transaction.pending-withdrawal', $data);
    }

    // active withdrawal list
    public function adminActiveWithdrawal(Request $request)
    {
        $data['title'] = __('Active Withdrawal');
        if ($request->ajax()) {
            $withdrawal = WithdrawHistory::select(
                'withdraw_histories.address'
                , 'withdraw_histories.amount'
                , 'withdraw_histories.fees'
                , 'withdraw_histories.transaction_hash'
                , 'withdraw_histories.confirmations'
                , 'withdraw_histories.address_type as addr_type'
                , 'withdraw_histories.updated_at'
                , 'withdraw_histories.wallet_id'
                , 'withdraw_histories.receiver_wallet_id'
                , 'withdraw_histories.coin_type'
            )->where(['withdraw_histories.status' => STATUS_SUCCESS]);

            return datatables()->of($withdrawal)
                ->addColumn('address_type', function ($wdrl) {
                    return addressType($wdrl->addr_type);
                })
                ->addColumn('coin_type', function ($wdrl) {
                    return check_default_coin_type($wdrl->coin_type);
                })
                ->addColumn('sender', function ($wdrl) {
                    return isset($wdrl->senderWallet->user) ? $wdrl->senderWallet->user->first_name . ' ' . $wdrl->senderWallet->user->last_name : '';
                })
                ->addColumn('receiver', function ($wdrl) {
                    return isset($wdrl->receiverWallet->user) ? $wdrl->receiverWallet->user->first_name . ' ' . $wdrl->receiverWallet->user->last_name : '';
                })
                ->make(true);
        }

        return view('admin.transaction.pending-withdrawal', $data);
    }

    // accept process of pending withdrawal
    public function adminAcceptPendingWithdrawal($id)
    {
        try {
            if (isset($id)) {
                try {
                    $wdrl_id = decrypt($id);
                } catch (\Exception $e) {
                    return redirect()->back();
                }
                $transaction = WithdrawHistory::with('wallet')->with('users')->where(['id' => $wdrl_id, 'status' => STATUS_PENDING])->firstOrFail();


                if (!empty($transaction)) {

                    $mail_info = [];
                    $sender_wallet_address = WalletAddressHistory::where('wallet_id',$transaction->wallet_id)->first()->address;
                    $coin_name = strtolower($transaction->wallet->coin_type);
                    $mail_info['mailTemplate'] = 'email.transaction_mail';
                    $withdraw_status = 'Successful';

                    if ($transaction->address_type == ADDRESS_TYPE_INTERNAL) {

                        $deposit = DepositeTransaction::where(['transaction_id' =>$transaction->transaction_hash, 'address' => $transaction->address])->update(['status' => STATUS_SUCCESS]);

                        Wallet::where(['id' => $transaction->receiver_wallet_id])->increment('balance', $transaction->amount);
                        $transaction->status = STATUS_SUCCESS;
                        $transaction->save();

                        $wallet_name = $transaction->wallet->name;
                        $sender_info = User::find($transaction->user_id);
                        $mail_info['to'] = $sender_info->email;
                        $mail_info['name'] = $sender_info->first_name.' '.$sender_info->last_name;
                        $mail_info_address_type = 'Internal';
                        $mail_info['subject'] = "TransactionID:<$transaction->transaction_hash> Withdrawal ($transaction->amount $coin_name) approved.";
                        $mail_info['email_message']="$transaction->amount $coin_name Withdrawal approved from $wallet_name. Transaction Information given below:";
                        $mail_info['email_message_table'] = "<table>
                        <tbody>
                            <tr>
                                <td>Sender Address</td>
                                <td>$sender_wallet_address</td>
                            </tr>
                            <tr>
                                <td>Receiver Address</td>
                                <td>$transaction->address</td>
                            </tr>
                            <tr>
                                <td>Address Type</td>
                                <td>$mail_info_address_type</td>
                            </tr>
                            <tr>
                                <td>TransactionID</td>
                                <td>$transaction->transaction_hash</td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td>$transaction->amount $coin_name</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>$withdraw_status</td>
                            </tr>
                        </tbody>
                    </table>";
                        dispatch(new MailSend($mail_info))->onQueue('send-mail-withdrawal');

                        $receiver_wallet_address = Wallet::where('id',$transaction->receiver_wallet_id)->first();
                        $wallet_name = $receiver_wallet_address->name;
                        $sender_info = $receiver_wallet_address->user;
                        $mail_info['to'] = $sender_info->email;
                        $mail_info['name'] = $sender_info->first_name.' '.$sender_info->last_name;
                        $mail_info_address_type = 'Internal';
                        $mail_info['subject'] = "TransactionID:<$transaction->transaction_hash> Deposit ($transaction->amount $coin_name) approved.";
                        $mail_info['email_message']="$transaction->amount $coin_name Deposit approved from $wallet_name. Transaction Information given below:";
                        $mail_info['email_message_table'] = "<table>
                        <tbody>
                            <tr>
                                <td>Sender Address</td>
                                <td>$sender_wallet_address</td>
                            </tr>
                            <tr>
                                <td>Receiver Address</td>
                                <td>$transaction->address</td>
                            </tr>
                            <tr>
                                <td>Address Type</td>
                                <td>$mail_info_address_type</td>
                            </tr>
                            <tr>
                                <td>TransactionID</td>
                                <td>$transaction->transaction_hash</td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td>$transaction->amount $coin_name</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>$withdraw_status</td>
                            </tr>
                        </tbody>
                    </table>";
                        dispatch(new MailSend($mail_info))->onQueue('send-mail-deposit');

                        return redirect()->back()->with('success', 'Pending withdrawal accepted Successfully.');

                    } elseif ($transaction->address_type == ADDRESS_TYPE_EXTERNAL) {
                        try {
                            if ($transaction->coin_type == DEFAULT_COIN_TYPE) {
                                $settings = allsetting();
                                $coinApi = new ERC20TokenApi();
                                $requestData = [
                                    "amount_value" => (float)$transaction->amount,
                                    "from_address" => $settings['wallet_address'] ?? '',
                                    "to_address" => $transaction->address,
                                    "contracts" => $settings['private_key'] ?? ''
                                ];
                                $result = $coinApi->sendCustomToken($requestData);
                                if ($result['success'] ==  true) {
                                    $transaction->transaction_hash = $result['data']->hash;
                                    $transaction->used_gas = $result['data']->used_gas;
                                    $transaction->status = STATUS_SUCCESS;
                                    $transaction->update();
                                    dispatch(new DistributeWithdrawalReferralBonus($transaction))->onQueue('referral');
//                                    $bonus = $affiliate_servcice->storeAffiliationHistory($transaction);


                                    $wallet_name = $transaction->wallet->name;
                                    $sender_info = User::find($transaction->user_id);
                                    $mail_info['to'] = $sender_info->email;
                                    $mail_info['name'] = $sender_info->first_name.' '.$sender_info->last_name;
                                    $mail_info_address_type = 'External';
                                    $mail_info['subject'] = "TransactionID:<$transaction->transaction_hash> Withdrawal ($transaction->amount $coin_name) approved.";
                                    $mail_info['email_message']="$transaction->amount $coin_name Withdrawal approved from $wallet_name. Transaction Information given below:";
                                    $mail_info['email_message_table'] = "<table>
                        <tbody>
                            <tr>
                                <td>Sender Address</td>
                                <td>$sender_wallet_address</td>
                            </tr>
                            <tr>
                                <td>Receiver Address</td>
                                <td>$transaction->address</td>
                            </tr>
                            <tr>
                                <td>Address Type</td>
                                <td>$mail_info_address_type</td>
                            </tr>
                            <tr>
                                <td>TransactionID</td>
                                <td>$transaction->transaction_hash</td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td>$transaction->amount $coin_name</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>$withdraw_status</td>
                            </tr>
                        </tbody>
                    </table>";
                                    dispatch(new MailSend($mail_info))->onQueue('send-mail-withdrawal');


                                    return redirect()->back()->with('success', __('Pending withdrawal accepted Successfully.'));
                                } else {
                                    return redirect()->back()->with('dismiss', $result['message']);
                                }
                            } else {
                                $currency =  $transaction->coin_type;
                                $coinpayment = new CoinPaymentsAPI();
                                $response = $coinpayment->CreateWithdrawal($transaction->amount,$currency,$transaction->address);

                                if (is_array($response) && isset($response['error']) && ($response['error'] == 'ok') ) {
                                    $transaction->transaction_hash = $response['result']['id'];
                                    $transaction->status = STATUS_SUCCESS;
                                    $transaction->update();
//                                    $bonus = $affiliate_servcice->storeAffiliationHistory($transaction);
                                    dispatch(new DistributeWithdrawalReferralBonus($transaction))->onQueue('referral');

                                    $wallet_name = $transaction->wallet->name;
                                    $sender_info = User::find($transaction->user_id);
                                    $mail_info['to'] = $sender_info->email;
                                    $mail_info['name'] = $sender_info->first_name.' '.$sender_info->last_name;
                                    $mail_info_address_type = 'External';
                                    $mail_info['subject'] = "TransactionID:<$transaction->transaction_hash> Withdrawal ($transaction->amount $coin_name) approved.";
                                    $mail_info['email_message']="$transaction->amount $coin_name Withdrawal approved from $wallet_name. Transaction Information given below:";
                                    $mail_info['email_message_table'] = "<table>
                        <tbody>
                            <tr>
                                <td>Sender Address</td>
                                <td>$sender_wallet_address</td>
                            </tr>
                            <tr>
                                <td>Receiver Address</td>
                                <td>$transaction->address</td>
                            </tr>
                            <tr>
                                <td>Address Type</td>
                                <td>$mail_info_address_type</td>
                            </tr>
                            <tr>
                                <td>TransactionID</td>
                                <td>$transaction->transaction_hash</td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td>$transaction->amount $coin_name</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>$withdraw_status</td>
                            </tr>
                        </tbody>
                    </table>";
                                    dispatch(new MailSend($mail_info))->onQueue('send-mail-withdrawal');

                                    return redirect()->back()->with('success', __('Pending withdrawal accepted Successfully.'));

                                } else {
                                    return redirect()->back()->with('dismiss', $response['error']);
                                }
                            }

                        } catch(\Exception $e) {
                            Log::info('adminAcceptPendingWithdrawal --> '.$e->getMessage());
                            return redirect()->back()->with('dismiss', $e->getMessage());
                        }
                    }
                } else {
                    return redirect()->back()->with('dismiss', __('Transaction not found'));
                }
            }
            return redirect()->back()->with('dismiss', __('Something went wrong! Please try again!'));
        } catch (\Exception $e) {
            Log::info('adminAcceptPendingWithdrawal --> '.$e->getMessage());
            return redirect()->back()->with('dismiss', $e->getMessage());
        }

    }

    // pending withdrawal reject process
    public function adminRejectPendingWithdrawal($id)
    {
        if (isset($id)) {
            try {
                $wdrl_id = decrypt($id);
            } catch (\Exception $e) {
                return redirect()->back();
            }
            $transaction = WithdrawHistory::where(['id' => $wdrl_id, 'status' => STATUS_PENDING])->firstOrFail();

            if (!empty($transaction)) {

                $mail_info = [];
                $sender_wallet_address = WalletAddressHistory::where('wallet_id',$transaction->wallet_id)->first()->address;
                $coin_name = strtolower($transaction->wallet->coin_type);
                $mail_info['mailTemplate'] = 'email.transaction_mail';
                $withdraw_status = 'Rejected';

                if ($transaction->address_type == ADDRESS_TYPE_INTERNAL) {

                    Wallet::where(['id' => $transaction->wallet_id])->increment('balance', $transaction->amount);
                    $transaction->status = STATUS_REJECTED;
                    $transaction->update();

                    $wallet_name = $transaction->wallet->name;
                    $sender_info = User::find($transaction->user_id);
                    $mail_info['to'] = $sender_info->email;
                    $mail_info['name'] = $sender_info->first_name.' '.$sender_info->last_name;
                    $mail_info_address_type = 'Internal';
                    $mail_info['subject'] = "TransactionID:<$transaction->transaction_hash> Withdrawal ($transaction->amount $coin_name) rejected.";
                    $mail_info['email_message']="$transaction->amount $coin_name Withdrawal rejected from $wallet_name. Transaction Information given below:";
                    $mail_info['email_message_table'] = "<table>
                        <tbody>
                            <tr>
                                <td>Sender Address</td>
                                <td>$sender_wallet_address</td>
                            </tr>
                            <tr>
                                <td>Receiver Address</td>
                                <td>$transaction->address</td>
                            </tr>
                            <tr>
                                <td>Address Type</td>
                                <td>$mail_info_address_type</td>
                            </tr>
                            <tr>
                                <td>TransactionID</td>
                                <td>$transaction->transaction_hash</td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td>$transaction->amount $coin_name</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>$withdraw_status</td>
                            </tr>
                        </tbody>
                    </table>";
                    dispatch(new MailSend($mail_info))->onQueue('send-mail-withdrawal');

                    $receiver_wallet_address = Wallet::where('id',$transaction->receiver_wallet_id)->first();
                    $wallet_name = $receiver_wallet_address->name;
                    $sender_info = $receiver_wallet_address->user;
                    $mail_info['to'] = $sender_info->email;
                    $mail_info['name'] = $sender_info->first_name.' '.$sender_info->last_name;
                    $mail_info_address_type = 'Internal';
                    $mail_info['subject'] = "TransactionID:<$transaction->transaction_hash> Deposit ($transaction->amount $coin_name) rejected.";
                    $mail_info['email_message']="$transaction->amount $coin_name Deposit rejected from $wallet_name. Transaction Information given below:";
                    $mail_info['email_message_table'] = "<table>
                        <tbody>
                            <tr>
                                <td>Sender Address</td>
                                <td>$sender_wallet_address</td>
                            </tr>
                            <tr>
                                <td>Receiver Address</td>
                                <td>$transaction->address</td>
                            </tr>
                            <tr>
                                <td>Address Type</td>
                                <td>$mail_info_address_type</td>
                            </tr>
                            <tr>
                                <td>TransactionID</td>
                                <td>$transaction->transaction_hash</td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td>$transaction->amount $coin_name</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>$withdraw_status</td>
                            </tr>
                        </tbody>
                    </table>";
                    dispatch(new MailSend($mail_info))->onQueue('send-mail-deposit');

                    $deposit = DepositeTransaction::where(['transaction_id' =>$transaction->transaction_hash, 'address' => $transaction->address])->update(['status' => STATUS_REJECTED]);

                    return redirect()->back()->with('success', 'Pending withdrawal rejected Successfully.');
                } elseif ($transaction->address_type == ADDRESS_TYPE_EXTERNAL) {
                    Wallet::where(['id' => $transaction->wallet_id])->increment('balance', $transaction->amount);
                    $transaction->status = STATUS_REJECTED;

                    $transaction->update();

                    $wallet_name = $transaction->wallet->name;
                    $sender_info = User::find($transaction->user_id);
                    $mail_info['to'] = $sender_info->email;
                    $mail_info['name'] = $sender_info->first_name.' '.$sender_info->last_name;
                    $mail_info_address_type = 'External';
                    $mail_info['subject'] = "TransactionID:<$transaction->transaction_hash> Withdrawal ($transaction->amount $coin_name) rejected.";
                    $mail_info['email_message']="$transaction->amount $coin_name Withdrawal rejected from $wallet_name. Transaction Information given below:";
                    $mail_info['email_message_table'] = "<table>
                        <tbody>
                            <tr>
                                <td>Sender Address</td>
                                <td>$sender_wallet_address</td>
                            </tr>
                            <tr>
                                <td>Receiver Address</td>
                                <td>$transaction->address</td>
                            </tr>
                            <tr>
                                <td>Address Type</td>
                                <td>$mail_info_address_type</td>
                            </tr>
                            <tr>
                                <td>TransactionID</td>
                                <td>$transaction->transaction_hash</td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td>$transaction->amount $coin_name</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>$withdraw_status</td>
                            </tr>
                        </tbody>
                    </table>";
                    dispatch(new MailSend($mail_info))->onQueue('send-mail-withdrawal');

                    return redirect()->back()->with('success', __('Pending Withdrawal rejected Successfully.'));
                }
            }

            return redirect()->back()->with('dismiss', __('Something went wrong! Please try again!'));
        }
    }

    // gas send history
    public function adminGasSendHistory(Request $request)
    {
        $data['title'] = __('Admin Estimate Gas Sent History');
        if ($request->ajax()) {
            $items = EstimateGasFeesTransactionHistory::select('*');

            return datatables()->of($items)
                ->addColumn('created_at', function ($item) {
                    return $item->created_at;
                })
                ->addColumn('status', function ($item) {
                    return deposit_status($item->status);
                })
                ->make(true);
        }

        return view('admin.transaction.gas_sent_history', $data);
    }

    // token receive history
    public function adminTokenReceiveHistory(Request $request)
    {
        $data['title'] = __('Admin Token Receive History');
        if ($request->ajax()) {
            $items = AdminReceiveTokenTransactionHistory::select('*');

            return datatables()->of($items)
                ->addColumn('created_at', function ($item) {
                    return $item->created_at;
                })
                ->addColumn('status', function ($item) {
                    return deposit_status($item->status);
                })
                ->make(true);
        }

        return view('admin.transaction.token_receive_history', $data);
    }
}
