<div class="row">
    <div class="col-lg-4 offset-lg-1">
        <div class="form-area cp-user-profile-info withdraw-form">
            <div class="form-group d-none after_connect" id="amount">
                <input type="hidden" name="chain_id" id="chain_id" value="{{allsetting('chain_id')}}">
                <label for="amount">{{__('Amount')}}</label>
                <input name="amount" type="text" class="form-control" id="amount_input"
                       placeholder="Amount">
                <p class="text-warning" id="equ_btc"><span class="totalBTC"></span>
                    <span class="coinType"></span></p>
            </div>
            <button onclick="connectWithMetamask()" type="button"
                    class="btn profile-edit-btn before_connect">{{__('Confirm with metamask')}}
            </button>

            <button onclick="depositeFromMetamask()" type="button"
                    class="btn profile-edit-btn d-none after_connect">{{__('Pay with metamask')}}
            </button>

        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <h5 class="card-header">{{__("Metamask setup")}}</h5>
            <div class="card-body">
                <p>{{__('By using metamask you can deposit your token')}}</p>
                <p>{{__('Download metamask from there')}} <a target="_blank" href="https://metamask.io/">{{__('Metamax')}}</a></p>
                <p>{{__('Add token to your metamask wallet')}}</p>
                <p> <label for="">{{__('Chain link')}} : </label></p>
                <p>
                    <label for="">{{allsetting('chain_link')}}</label>
                </p>
                <p>
                    <label for="">
                        {{__('Contract address')}} :
                    </label>
                </p>
                <p>
                    <label for="">
                        {{allsetting('contract_address')}}
                    </label>
                    <input type="hidden" id="contract_address" value="{{allsetting('contract_address')}}">
                    <input type="hidden" id="wallet_address" value="{{allsetting('wallet_address')}}">
                    <input type="hidden" id="callback_url" value="{{route('defaultDepositCallback')}}">
                </p>
            </div>
        </div>

    </div>
</div>
