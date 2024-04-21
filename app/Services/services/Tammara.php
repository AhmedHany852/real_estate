<?php

namespace App\Services\services;

use Illuminate\Http\Request;
use App\Models\PaymentGetway;
use App\Models\PaymentGeteway;
use Illuminate\Support\Facades\Config;
use App\Services\contracts\PaymentInterface;

class Tammara implements PaymentInterface
{
    public function __construct()
    {

        $tammara = PaymentGetway::where([
            ['keyword', 'Tammara'],
        ])->first();
        $tammaraConf = json_decode($tammara->information, true);
        Config::set('services.tammara.api_token', $tammaraConf["api_token"]);
        Config::set('services.tammara.base_url','');


    }
    public function paymentProcess(
        $request,
        $_amount,
        $return,
        $callback
    ){
        $myfatoorah =   Config::get('services.tammara.api_token');
    }
    public function successPayment(Request $request)
    {



    }
    public function calbackPayment(Request $request)
    {




    }
}
