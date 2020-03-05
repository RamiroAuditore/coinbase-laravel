# coinbase-laravel
A wrapper for Coinbase Commerce's API for Laravel projects that require token purchases

## Notes
Right now the package just handles creating charges and has a webhook for the updates of charges. I might add the rest of endpoints in the future.

## Installation
Install with Composer:
```
composer require ramiroauditore/coinbase-laravel
```

Then you need to include the ServiceProvider in the <i>config/app.php</i> file like this:
```php
/*
 * Package Service Providers...
 */
coinbaselaravel\CoinbaseLaravelServiceProvider::class
```

Add the following to your <i>.env</i> file:
```
COINBASE_API_KEY=<YOUR_API_KEY> //An API Key you generated from your Coinbase's dashboard
TOKENS_BY_DOLLAR=<YOUR_TOKENS_PER_DOLLAR_EQUIVALENCE> //The ammount of tokens that should be given to the user per dollar spent
```

Run `php artisan: migrate` and a migration will run, adding the `coinbase_transactions` table to your database.

## Usage
### Create a charge
To create a charge you will need a `POST` to the following route:
```
https://yoursite.com/create_charge
```
You will need to send the following data:
```
user_id: An id from the users table so you know who the tokens belong to
amount: The amount in USD that the user wishes to spend
token_amount: The amount of tokens that the user will receive should they pay the value from the amount field
```
Should the request be successful a new row will be added to your coinbase_transactions with the charge data and you will receive the following JSON response:
```javascript
{
"pricing":{
    "local":
        {"amount":"0.50","currency":"USD"},
    "bitcoincash":
        {"amount":"0.00145658","currency":"BCH"},
    "litecoin":
        {"amount":"0.00792959","currency":"LTC"},
    "bitcoin":
        {"amount":"0.00005480","currency":"BTC"},
    "ethereum":
        {"amount":"0.002146000","currency":"ETH"},
    "usdc":
        {"amount":"0.500000","currency":"USDC"},
    "dai":
        {"amount":"0.498643440520065163","currency":"DAI"}
    },
"qr_strings":{
  "bitcoincash":"qrztp9f44tkdplvk822prfqqtep820hfs5hqhrktzc",
  "litecoin":"M97sEjpXCih4RHZiNtR6h3H8pZt134ECTt",
  "bitcoin":"37YVDwLSJbfUggqt3K6reBJS1awoXiEU2s",
  "ethereum":"0x0275b18043776cc73b57ebe980b58c521bf5ce67",
  "usdc":"0x0275b18043776cc73b57ebe980b58c521bf5ce67",
  "dai":"0x0275b18043776cc73b57ebe980b58c521bf5ce67"
  }
}
```
In which each object in the pricing object represents the equivalent of the local (USD) price the user submitted and each property in the qr_strings object represents a string you can use to generate a QR code to pay with the respective currency.
### Webhooks
The webhook function resides in the following route:
```
https://yoursite.com/charge_update
```
So you should exclude it from the CSRF protection by modfying your `Http/Middleware/VerifyCsrfToken.php` file like this:
```php
protected $except = [
  'charge_update/'
];
```
After doing that you should register the webhook by providing the URL of the route in the settings of you Coinbase Commerce and it should automatically start functioning whenever there's an event (I recommend disabling the `charge:created` hook). It will update the corresponding table entry with the new data. Including doing recalculations of the `amount` and `token_amount` fields to adjust in case the user underpaid or overpaid.

<b>NOTE:</b> Keep in mind, though, that if the user does over or underpay and you want the charge to still be valid, you will need to "resolve it" manually through the dashboard, otherwise it will be marked as failed and the money will be returned to the user. There's a way to do this through an endpoint, I might add it later.
