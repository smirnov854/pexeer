<div class="header-bar">
    <div class="table-title">
        <h3>{{__('Coin Payment Details')}}</h3>
    </div>
</div>
<div class="profile-info-form">
    <form action="{{route('adminSavePaymentSettings')}}" method="post"
          enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-6 col-12 mt-20">
                <div class="form-group">
                    <label for="#">{{__('COIN PAYMENT PUBLIC KEY')}}</label>
                    <input class="form-control" type="text" name="COIN_PAYMENT_PUBLIC_KEY"
                           autocomplete="off" placeholder=""
                           value="{{settings('COIN_PAYMENT_PUBLIC_KEY')}}">
                </div>
            </div>
            <div class="col-lg-6 col-12 mt-20">
                <div class="form-group">
                    <label for="#">{{__('COIN PAYMENT PRIVATE KEY')}}</label>
                    <input class="form-control" type="text" name="COIN_PAYMENT_PRIVATE_KEY"
                           autocomplete="off" placeholder=""
                           value="{{settings('COIN_PAYMENT_PRIVATE_KEY')}}">
                </div>
            </div>
            <div class="col-lg-6 col-12 mt-20">
                <div class="form-group">
                    <label for="#">{{__('COIN PAYMENT IPN MERCHANT ID')}}</label>
                    <input class="form-control" type="text" name="ipn_merchant_id"
                           autocomplete="off" placeholder=""
                           value="{{isset(settings()['ipn_merchant_id']) ? settings('ipn_merchant_id') : ''}}">
                </div>
            </div>
            <div class="col-lg-6 col-12 mt-20">
                <div class="form-group">
                    <label for="#">{{__('COIN PAYMENT IPN SECRET')}}</label>
                    <input class="form-control" type="text" name="ipn_secret"
                           autocomplete="off" placeholder=""
                           value="{{isset(settings()['ipn_secret']) ? settings('ipn_secret') : ''}}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-2 col-12 mt-20">
                <button type="submit" class="button-primary theme-btn">{{__('Update')}}</button>
            </div>
        </div>
    </form>
</div>
