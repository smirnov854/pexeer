<div class="row">
    <div class="col-md-6">
        <div class="cp-user-profile-info">
            <form class="mt-4" method="POST"
                  action="">
                @csrf
                <input type="hidden" id="temp" value="">

                <div class="form-group mt-4">
                    <label>{{__('Coin Amount')}}</label>
                    <input name="amount"  type="" id="amount2" placeholder="{{__('Coin')}}"
                           class="form-control number_only confirm" value="{{old('amount')}}">
                    <span class="text-warning" style="font-weight: 700;">{{__('Minimum amount : ')}}</span>
                    <span class="text-warning">{{$wallet->minimum_withdrawal}} {{settings('coin_name')}}</span>
                    <span class="text-warning">{{__(' and ')}}</span>
                    <span class="text-warning" style="font-weight: 700;">{{__('Maximum amount : ')}}</span>
                    <span class="text-warning">{{number_format($wallet->maximum_withdrawal,2)}} {{settings('coin_name')}}</span>
                </div>
                <div class="form-group m4">
                    <label>{{__('Address')}}</label>
                    <input name="address"  type="" placeholder="{{__('address')}}" id="address"
                           class="form-control " value="">
                </div>
                <div class="form-group m-0">
                    <button onclick="withdrwalAmount(this)" class="btn theme-btn confirm"
                            type="button">{{__('Confirm')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
