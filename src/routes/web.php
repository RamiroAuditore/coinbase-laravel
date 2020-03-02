<?php
    use CoinbaseCommerce\ApiClient;
    // MyVendor\contactform\src\routes\web.php
    Route::get('contact', function(){
        return ApiClient::init('API_KEY');
    });
?>