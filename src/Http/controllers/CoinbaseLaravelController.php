<?php
    // MyVendor\Contactform\src\Http\Controllers\ContactFormController.php
    namespace coinbaselaravel\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use coinbaselaravel\Models\CoinbaseLaravel;
    use CoinbaseCommerce\ApiClient;
    use CoinbaseCommerce\Resources\Charge;
    use Config;

    class CoinbaseLaravelController extends Controller {

        public function index()
        {
           return view('contactform::contact');
        }

        public function create_charge(Request $request)
        {
            // $test_response = new \stdClass();
            // $test_response->status = "Pending";
            // $test_response_json = json_encode($test_response);
            // CoinbaseLaravel::create(array_merge($request->all(), ['transaction_response' => $test_response_json]));
            // return redirect('/');tkm
            ApiClient::init(env("COINBASE_API_KEY"));
            $chargeData = [
                'name' => 'Compra en XOYcoin',
                'description' => 'La siguiente transacción se realiza para adquisición de tokens en XOYcoin',
                'local_price' => [
                    'amount' => $request->amount,
                    'currency' => $request->currency
                ],
                'pricing_type' => 'fixed_price'
            ];
            
            $charge = Charge::create($chargeData);
            $qr_string = $charge->$request->currency;
            return dd($qr_string);
        }


    }