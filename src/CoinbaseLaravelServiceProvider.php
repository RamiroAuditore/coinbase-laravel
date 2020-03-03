<?php
    // MyVendor\contactform\src\ContactFormServiceProvider.php
    namespace coinbaselaravel;
    use Illuminate\Support\ServiceProvider;
    class CoinbaseLaravelServiceProvider extends ServiceProvider {
        public function boot()
        {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
            $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
        }
        public function register()
        {
        }
    }
    ?>