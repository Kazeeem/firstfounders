<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Paystack;

class PaymentController extends Controller
{
    public function genTranxRef()
    {
        // $tran = Paystack::genTranxRef();
        $tran = $this->generateToken();
        return response()->json(['ref' => $tran]);
    }

    public function generateToken($length=13) {
        $token = openssl_random_pseudo_bytes($length);  //Generate a random string.
        $token = bin2hex($token); //Convert the binary data into hexadecimal representation. P.S: This function doubles the number of strings specified in the function above.
        return $token;
    }

    public function index()
    {
        return view('paystack');
    }
    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway()
    {
        try{
            return Paystack::getAuthorizationUrl()->redirectNow();
        }catch(\Exception $e) {
            return Redirect::back()->withMessage(['msg'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
        }        
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();

        $response = json_decode(json_encode($paymentDetails));

        $authorization_code = $response->data->authorization->authorization_code;
        $card_expiry_month = $response->data->authorization->exp_month;
        $card_expiry_year = $response->data->authorization->exp_year;
        $card_type = $response->data->authorization->card_type;
        $bank_name = $response->data->authorization->bank;
        $user_email = $response->data->customer->email;
        $transaction_reference = $response->data->reference;

        dd($authorization_code, $card_expiry_month, $card_expiry_year, $card_type, $bank_name, $user_email, $transaction_reference);
        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want
    }
}
