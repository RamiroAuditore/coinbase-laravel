<?php
    // MyVendor\contactform\src\ContactFormServiceProvider.php
    namespace coinbaselaravel;
    use Illuminate\Support\ServiceProvider;
    class CoinbaseLaravelServiceProvider extends ServiceProvider {
        public function boot()
        {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        }
        public function register()
        {
        }
    }
    ?>