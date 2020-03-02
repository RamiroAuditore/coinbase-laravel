<?php
    use CoinbaseCommerce\ApiClient;
    use CoinbaseCommerce\Resources\Charge;
    // MyVendor\contactform\src\routes\web.php
    Route::get('contact', function(){
        ApiClient::init("API_KEY");
        $chargeList = Charge::getList(["limit" => 5]);
        return dd($chargeList);
    });
?>