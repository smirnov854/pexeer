<?php
/**
 * Created by PhpStorm.
 * User: bacchu
 * Date: 1/25/22
 * Time: 5:19 PM
 */

namespace App\Repository;

use App\Model\AdminReceiveTokenTransactionHistory;
use App\Model\DepositeTransaction;
use App\Model\EstimateGasFeesTransactionHistory;
use App\Model\WalletAddressHistory;
use App\Services\ERC20TokenApi;
use App\Services\Logger;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;


class CustomTokenRepository
{
    private $tokenApi;
    private $logger;
    private $settings;
    private $walletAddress;
    private $walletPk;
    private $contractCoinName;

    public function __construct()
    {
        $this->tokenApi = new ERC20TokenApi();
        $this->logger = new Logger();
        $this->settings = allsetting();
        $this->walletAddress = $this->settings['wallet_address'] ?? '';
        $this->walletPk = $this->settings['private_key'] ?? '';
        $this->contractCoinName = $this->settings['contract_coin_name'] ?? 'ETH';
    }

    public function depositCustomToken()
    {
//        DB::beginTransaction();
        try {
            $latestTransactions = $this->getLatestTransactionFromBlock();
            if ($latestTransactions['success'] == true) {
//                $count = sizeof($latestTransactions['data']);
                foreach($latestTransactions['data'] as $transaction) {
                    $checkDeposit = $this->checkAddressAndDeposit($transaction->to_address,$transaction->tx_hash,$transaction->amount);
                    if ($checkDeposit['success'] == true) {
                        $deposit = $checkDeposit['data'];
                        $userPk = get_wallet_personal_add($transaction->to_address,$checkDeposit['pk']);
                        $checkGasFees = $this->checkEstimateGasFees($transaction->to_address,$transaction->amount);
                        if($checkGasFees['success'] == true) {
                            $this->logger->log('Estimate gas ',$checkGasFees['data']->estimateGasFees);
                            $estimateFees = $checkGasFees['data']->estimateGasFees;
                            $gas = bcadd($estimateFees , (bcdiv(bcmul($estimateFees, 90,8),100,8)));
                            $this->logger->log('Gas',$gas);

                            $checkAddressBalance = $this->checkWalletAddressBalance($transaction->to_address);
                            if ($checkAddressBalance['success'] == true) {
                                $walletNetBalance = $checkAddressBalance['data']->net_balance;
                                $this->logger->log('$walletNetBalance',$walletNetBalance);
                                if ($walletNetBalance >= $gas) {
                                    $estimateGas = 0;
                                    $this->logger->log('$estimateGas 0 ',$estimateGas);
                                } else {
                                    $estimateGas = bcsub($gas, $walletNetBalance,8);
                                    $this->logger->log('$estimateGas have ',$estimateGas);
                                }
                                if ($estimateGas > 0) {
                                    $this->logger->log('sendFeesToUserAddress ',$estimateGas);
                                    $sendFees = $this->sendFeesToUserAddress($transaction->to_address,$estimateGas,$deposit->receiver_wallet_id,$deposit->id);
                                    if ($sendFees['success'] == true) {
                                        $receiveToken = $this->receiveTokenFromUserAddress($transaction->to_address,$transaction->amount,$userPk,$deposit->id);
                                        if ($receiveToken['success'] == true) {
                                            $this->updateUserWallet($deposit,$receiveToken['data']->hash);
                                        } else {
                                            $this->logger->log('depositCustomToken', 'token received process failed');
                                        }
                                    } else {
                                        $this->logger->log('depositCustomToken', 'send fees process failed');
                                    }
                                } else {
                                    $this->logger->log('sendFeesToUserAddress ', 'no gas needed');
                                    $receiveToken = $this->receiveTokenFromUserAddress($transaction->to_address,$transaction->amount,$userPk,$deposit->id);
                                    if ($receiveToken['success'] == true) {
                                       $this->updateUserWallet($deposit,$receiveToken['data']->hash);
                                    } else {
                                        $this->logger->log('depositCustomToken', 'token received process failed');
                                    }
                                }
                            } else {
                                $this->logger->log('depositCustomToken', 'get balance failed');
                            }
                        } else {
                            $this->logger->log('depositCustomToken', 'check gas fees calculate failed');
                        }
                    }
                }
            } else {
                $this->logger->log('depositCustomToken', $latestTransactions['message']);
            }
            return $latestTransactions;
        } catch (\Exception $e) {
//            DB::rollBack();
            $this->logger->log('depositCustomToken', $e->getMessage());
        }
    }

    // update wallet
    public function updateUserWallet($deposit,$hash)
    {
        try {
            DepositeTransaction::where(['id' => $deposit->id])
                ->update([
                    'status' => STATUS_SUCCESS,
                    'transaction_id' => $hash
                    ]);
            $userWallet = $deposit->receiverWallet;
            $this->logger->log('depositCustomToken', 'before update wallet balance => '. $userWallet->balance);
            $userWallet->increment('balance',$deposit->amount);
            $this->logger->log('depositCustomToken', 'after update wallet balance => '. $userWallet->balance);
            $this->logger->log('depositCustomToken', 'update one wallet id => '. $deposit->receiver_wallet_id);
            $this->logger->log('depositCustomToken', 'Deposit process success');
        } catch (\Exception $e) {
            $this->logger->log('updateUserWallet', $e->getMessage());
        }
    }

    // check address and deposit
    public function checkAddressAndDeposit($address,$hash,$amount)
    {
        try {
            $checkAddress = WalletAddressHistory::where(['address' => $address, 'coin_type' => DEFAULT_COIN_TYPE])->first();
            if(!empty($checkAddress)) {
                $checkDeposit = DepositeTransaction::where(['address' => $address, 'transaction_id' => $hash])->first();
                if ($checkDeposit) {
                    $this->logger->log('checkAddressAndDeposit', 'deposit already in db '.$hash);
                    $response = ['success' => false, 'message' => __('This hash already in db'), 'data' => []];
                } else {
                    $createDeposit = DepositeTransaction::create([
                        'address' => $address,
                        'receiver_wallet_id' => $checkAddress->wallet_id,
                        'address_type' => ADDRESS_TYPE_EXTERNAL,
                        'type' => $checkAddress->coin_type,
                        'amount' => $amount,
                        'doller' => bcmul($amount,settings('coin_price'),8),
                        'transaction_id' => $hash,
                        'unique_code' => uniqid().date('').time(),
                        'coin_id' => $checkAddress->wallet->coin_id
                    ]);
                    $this->logger->log('deposit', $createDeposit);
                    $response = ['success' => true, 'message' => __('New deposit'), 'data' => $createDeposit,'pk' => $checkAddress->pk];
                }
            } else {
                $this->logger->log('checkAddressAndDeposit', 'address not found in db '.$address);
                $response = ['success' => false, 'message' => __('This address not found in db'), 'data' => []];
            }
        } catch (\Exception $e) {
            $this->logger->log('checkAddressAndDeposit', $e->getMessage());
            $response = ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
        return $response;
    }

    // get latest transaction block data
    public function getLatestTransactionFromBlock()
    {
        $response = ['success' => false, 'message' => 'failed', 'data' => []];
        try {
            $result = $this->tokenApi->getContractTransferEvent();
            if ($result['success'] == true) {
                $response = ['success' => $result['success'], 'message' => $result['message'], 'data' => $result['data']->result];
            } else {
                $response = ['success' => false, 'message' => __('No transaction found'), 'data' => []];
            }

        } catch (\Exception $e) {
            $this->logger->log('getLatestTransactionFromBlock', $e->getMessage());
            $response = ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
        return $response;
    }

    // check estimate gas for sending token
    public function checkEstimateGasFees($address,$amount)
    {
        $response = ['success' => false, 'message' => 'failed', 'data' => []];
        try {
            $requestData = [
                "amount_value" => $amount,
                "from_address" => $address,
                "to_address" => $this->walletAddress
            ];
            $check = $this->tokenApi->checkEstimateGas($requestData);
            if ($check['success'] == true) {
                $response = ['success' => true, 'message' => $check['message'], 'data' => $check['data']];
            } else {
                $response = ['success' => false, 'message' => $check['message'], 'data' => []];
            }
        } catch (\Exception $e) {
            $this->logger->log('checkEstimateGasFees', $e->getMessage());
            $response = ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }

        return $response;
    }

    // send estimate gas fees to address
    public function sendFeesToUserAddress($address,$amount,$wallet_id,$depositId)
    {
        try {
            $requestData = [
            "amount_value" => $amount,
            "from_address" => $this->walletAddress,
            "to_address" => $address,
            "contracts" => $this->walletPk
        ];

        $result = $this->tokenApi->sendEth($requestData);
        if ($result['success'] == true) {
            $this->saveEstimateGasFeesTransaction($wallet_id,$result['data']->hash,$amount,$this->walletAddress,$address,$depositId);
            $response = ['success' => true, 'message' => __('Fess send successfully'), 'data' => []];
        } else {
            $this->logger->log('sendFeesToUserAddress', $result['message']);
            $response = ['success' => false, 'message' => $result['message'], 'data' => []];
        }
        } catch (\Exception $e) {
            $this->logger->log('sendFeesToUserAddress', $e->getMessage());
            $response = ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
        return $response;
    }

    // save estimate gas fees transaction
    public function saveEstimateGasFeesTransaction($wallet_id,$hash,$amount,$adminAddress,$userAddress,$depositId)
    {
        try {
           $data = EstimateGasFeesTransactionHistory::create([
                'unique_code' => uniqid().date('').time(),
                'wallet_id' => $wallet_id,
               'deposit_id' => $depositId,
                'amount' => $amount,
                'coin_type' => $this->contractCoinName,
                'admin_address' => $adminAddress,
                'user_address' => $userAddress,
                'transaction_hash' => $hash,
                'status' => STATUS_SUCCESS
            ]);
           $this->logger->log('saveEstimateGasFeesTransaction', json_encode($data));
        } catch (\Exception $e) {
            $this->logger->log('saveEstimateGasFeesTransaction', $e->getMessage());
        }
    }

    // receive token from user address
    public function receiveTokenFromUserAddress($address,$amount,$userPk,$depositId)
    {
        try {
            $requestData = [
                "amount_value" => $amount,
                "from_address" => $address,
                "to_address" => $this->walletAddress,
                "contracts" => $userPk
            ];
            $result = $this->tokenApi->sendCustomToken($requestData);
            if ($result['success'] == true) {
                $this->saveReceiveTransaction($result['data']->used_gas,$result['data']->hash,$amount,$this->walletAddress,$address,$depositId);
                $response = ['success' => true, 'message' => __('Token received successfully'), 'data' => $result['data']];
            } else {
                $this->logger->log('sendFeesToUserAddress', $result['message']);
                $response = ['success' => false, 'message' => $result['message'], 'data' => []];
            }
        } catch (\Exception $e) {
            $this->logger->log('sendFeesToUserAddress', $e->getMessage());
            $response = ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
        return $response;
    }

    // save receive token transaction
    public function saveReceiveTransaction($fees,$hash,$amount,$adminAddress,$userAddress,$depositId)
    {
        try {
            $data = AdminReceiveTokenTransactionHistory::create([
                'unique_code' => uniqid().date('').time(),
                'amount' => $amount,
                'deposit_id' => $depositId,
                'fees' => $fees,
                'to_address' => $adminAddress,
                'from_address' => $userAddress,
                'transaction_hash' => $hash,
                'status' => STATUS_SUCCESS
            ]);
            $this->logger->log('saveReceiveTransaction', json_encode($data));
        } catch (\Exception $e) {
            $this->logger->log('saveReceiveTransaction', $e->getMessage());
        }
    }

    // check wallet balance
    public function checkWalletAddressBalance($address)
    {
        try {
            $requestData = array(
                "type" => 1,
                "address" => $address,
                ""
            );
            $result = $this->tokenApi->checkWalletBalance($requestData);
            if ($result['success'] == true) {
                $response = ['success' => true, 'message' => __('Get balance'), 'data' => $result['data'] ];
            } else {
                $this->logger->log('sendFeesToUserAddress', $result['message']);
                $response = ['success' => false, 'message' => $result['message'], 'data' => []];
            }
        } catch (\Exception $e) {
            $this->logger->log('checkWalletAddressBalance', $e->getMessage());
            $response = ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
        return $response;
    }
}
