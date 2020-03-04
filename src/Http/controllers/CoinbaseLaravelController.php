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
            CoinbaseLaravel::create(['transaction_response' => serialize($charge), 'order_code' => $charge->code, 'order_id' => $charge->id, 'status' => $charge->timeline[$last_timeline_entry]['status'], 'user_id' => $request->user_id, 'amount' => $request->amount, 'currency' => $request->currency, 'token_amount' => $request->token_amount]);

            return response()->json([
                'pricing' => $charge->pricing,
                'qr_strings' => $charge->addresses
            ]);
        }

        public function charge_update(Request $request)
        {
            $tokens_per_usd = env("TOKENS_BY_DOLLAR");
            $total_usd = 0;
            $context = 'NO_CONTEXT';

            $last_timeline_entry = count($request->event["data"]["timeline"]) - 1;
            // return var_dump($request->event["data"]["timeline"][$last_timeline_entry]);
            if(isset($request->event["data"]["timeline"][$last_timeline_entry]['context'])){
                $context = $request->event["data"]["timeline"][$last_timeline_entry]['context'];
            }
            $charge_to_update = DB::table('coinbase_transactions')->where('order_code', $request->event["data"]["code"])->first();

            foreach ($request->event["data"]["payments"] as $key => $value) {
                if($value["status"] == "CONFIRMED"){
                    $total_usd+=$value["value"]["local"]["amount"];
                }
            }

            $total_tokens = $tokens_per_usd * $total_usd;

            $updated_charge = DB::table('coinbase_transactions')
            ->where('id', $charge_to_update->id)
            ->update(['status' => $request->event["data"]["timeline"][$last_timeline_entry]['status'], 'status_context' => $context, "updated_at" => \Carbon\Carbon::now(), 'transaction_response' => serialize($request->event), 'token_amount' => $total_tokens]);

            return $updated_charge;
        }

        public function test_sum()
        {
            $total_usd = 0;
            $tokens_per_usd = env("TOKENS_BY_DOLLAR");

            $charge = DB::table('coinbase_transactions')->where('order_code', 'BYB293VK')->first();

            $response_array = unserialize($charge->transaction_response);
            foreach ($response_array["data"]["payments"] as $key => $value) {
                if($value["status"] == "CONFIRMED"){
                    $total_usd+=$value["value"]["local"]["amount"];
                }
            }

            $total_tokens = $tokens_per_usd * $total_usd;

            dd($total_tokens);
        }

    }