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
                    'currency' => 'USD'
                ],
                'pricing_type' => 'fixed_price'
            ];

            try {
                $charge = Charge::create($chargeData);
            } catch (\Exception $exception) {
                echo sprintf("Unable to create charge. Error: %s \n", $exception->getMessage());
                return 'Error';
            }

            // $last_timeline_entry = end($charge->timeline)['status'];
            var_dump($charge->timeline);
            // CoinbaseLaravel::create(array_merge($request->all(), ['transaction_response' => json_encode($charge), 'order_id' => $charge->code, 'status' => $last_timeline_entry->status]));

            // foreach ($charge->addresses as $key => $value) {
            //     // $value = $value * 2;
            //     switch ($request->currency) {
            //         case "bitcoincash":
            //             $qr_string = $value;
            //             break;
            //         case "litecoin":
            //             $qr_string = $value;
            //             break;
            //         case "bitcoin":
            //             $qr_string = $value;
            //             break;
            //         case "ethereum":
            //             $qr_string = $value;
            //             break;
            //         case "ethereum":
            //             $qr_string = $value;
            //             break;
            //         case "ethereum":
            //             $qr_string = $value;
            //             break;
            //         default:
            //             $qr_string = "error";
            //     }
            // }

            CoinbaseLaravel::create(array_merge($request->all(), ['transaction_response' => json_encode($charge), 'order_id' => $charge->code]));

            // return dd($qr_string);
        }


    }