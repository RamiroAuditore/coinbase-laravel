<?php
    namespace coinbaselaravel\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use coinbaselaravel\Models\CoinbaseLaravel;
    use CoinbaseCommerce\ApiClient;
    use CoinbaseCommerce\Resources\Charge;
    use Config;
    use App\Models\Stage;

    class CoinbaseLaravelController extends Controller {

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
            CoinbaseLaravel::create(['transaction_response' => serialize($charge), 'order_code' => $charge->code, 'order_id' => $charge->id, 'status' => $charge->timeline[$last_timeline_entry]['status'], 'user_id' => $request->user_id, 'amount' => $request->amount, 'currency' => 'USD', 'token_amount' => $request->token_amount]);

            return response()->json([
                'pricing' => $charge->pricing,
                'qr_strings' => $charge->addresses
            ]);
        }

        public function charge_update(Request $request)
        {
            $current_stage = Stage::with('substages', 'bonuses_by_date', 'bonuses_by_tokens')->where('start_date', '<', Carbon::now())->where('end_date', '>', Carbon::now())->first();
            // $tokens_per_usd = env("TOKENS_BY_DOLLAR");
            $total_usd = 0;
            $context = 'NO_CONTEXT';

            $last_timeline_entry = count($request->event["data"]["timeline"]) - 1;
            // return dd($request->event["data"]["payments"]);
            if(isset($request->event["data"]["timeline"][$last_timeline_entry]['context'])){
                $context = $request->event["data"]["timeline"][$last_timeline_entry]['context'];
            }
            $charge_to_update = DB::table('coinbase_transactions')->where('order_code', $request->event["data"]["code"])->first();

            foreach ($request->event["data"]["payments"] as $key => $value) {
                if($value["status"] == "CONFIRMED"){
                    $total_usd+=$value["value"]["local"]["amount"];
                }
            }

            $total_tokens = $current_stage->base_price * $total_usd;

            $updated_charge = DB::table('coinbase_transactions')
            ->where('id', $charge_to_update->id)
            ->update(['status' => $request->event["data"]["timeline"][$last_timeline_entry]['status'], 'status_context' => $context, "updated_at" => \Carbon\Carbon::now(), 'transaction_response' => serialize($request->event), 'token_amount' => $total_tokens, 'amount' => $total_usd]);

            return $updated_charge;
        }

        public function resolve_charge($id)
        {
            ApiClient::init(env("COINBASE_API_KEY"));
            try {
                $retrievedCharge = Charge::retrieve($id);
            } catch (\Exception $exception) {
                return back()->with('error', 'Hubo un error, intenta de nuevo');
            }

            if($retrievedCharge){
                return back()->with('status', 'Se resolvió el cargo');
            } else {
                return back()->with('error', 'Hubo un error, intenta de nuevo');
            }
        }

    }