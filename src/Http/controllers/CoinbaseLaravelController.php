<?php
    // MyVendor\Contactform\src\Http\Controllers\ContactFormController.php
    namespace coinbaselaravel\Http\Controllers;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    // use coinbaselaravel\Models\CoinbaseLaravel;
    use CoinbaseCommerce\ApiClient;
    use CoinbaseCommerce\Resources\Charge;

    class CoinbaseLaravelController extends Controller {

        public function index()
        {
           return view('contactform::contact');
        }

        public function create_charge(Request $request)
        {
            ApiClient::init("API_KEY");
            $chargeList = Charge::getList(["limit" => 5]);
            return dd($chargeList);
        }


    }