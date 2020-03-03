<?php
    // MyVendor\Contactform\src\Http\Controllers\ContactFormController.php
    namespace coinbaselaravel\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use coinbaselaravel\Models\CoinbaseLaravel;
    use CoinbaseCommerce\ApiClient;
    use CoinbaseCommerce\Resources\Charge;

    class CoinbaseLaravelController extends Controller {

        public function index()
        {
           return view('contactform::contact');
        }

        public function create_charge(Request $request)
        {
            $test_response = new \stdClass();
            $test_response->status = "Pending";
            $test_response_json = json_encode($test_response);
            CoinbaseLaravel::create(array_merge($request->all(), ['response' => $test_response_json]));
            return redirect('/');
            // ApiClient::init("API_KEY");
            // $chargeList = Charge::getList(["limit" => 5]);
            // return dd($chargeList);
        }


    }