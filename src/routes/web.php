<?php
    Route::group(['namespace' => 'coinbaselaravel\Http\Controllers', 'middleware' => ['web']], function(){
        Route::post('create_charge', 'CoinbaseLaravelController@create_charge')->name('create_charge');
    });
?>