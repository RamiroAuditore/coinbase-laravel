<?php
    use CoinbaseCommerce\ApiClient;
    use CoinbaseCommerce\Resources\Charge;
    // MyVendor\contactform\src\routes\web.php
    Route::get('contact', function(){
        ApiClient::init("API_KEY");
        $chargeObj = Charge::retrieve("111");
        return dd($chargeObj);
    });
?>