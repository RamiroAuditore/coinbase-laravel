<?php
    // MyVendor\Contactform\src\Http\Controllers\ContactFormController.php
    namespace coinbaselaravel\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
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
                return response()->json([
                    'Error' => 'There was an error'
                ]);
            }

            $last_timeline_entry = count($charge->timeline) - 1;
            CoinbaseLaravel::create(['transaction_response' => json_encode($charge), 'order_code' => $charge->code, 'order_id' => $charge->id, 'status' => $charge->timeline[$last_timeline_entry]['status'], 'user_id' => $request->user_id, 'amount' => $request->amount, 'currency' => $request->currency]);

            return response()->json([
                'pricing' => $charge->pricing,
                'qr_strings' => $charge->addresses
            ]);
        }

        public function charge_update(Request $request)
        {
            $last_timeline_entry = count($request->event["data"]["timeline"]) - 1;
            $charge_to_update = DB::table('coinbase_transactions')->where('order_code', $request->event["data"]["code"])->first();
            $updated_charge = DB::table('coinbase_transactions')
            ->where('id', $charge_to_update->id)
            ->update(['status' => $request->event["data"]["timeline"][$last_timeline_entry]['status']]);

            return $updated_charge;
        }


    }