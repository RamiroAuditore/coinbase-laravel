<?php
    Route::name('coinbase')->group(function () {
        Route::get('/coinbase/create_charge', 'CoinbaseLaravelController@create_charge')->name('create_charge');
    });
?>