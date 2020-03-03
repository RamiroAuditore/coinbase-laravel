<?php
    Route::group(['namespace' => 'CoinbaseLaravel\Http\Controllers', 'middleware' => ['web']], function(){
        Route::get('create_charge', 'CoinbaseLaravelController@create_charge');
        // Route::post('contact', 'ContactFormController@sendMail')->name('contact');
    });
?>