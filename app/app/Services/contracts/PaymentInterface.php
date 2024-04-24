<?php

namespace App\Services\contracts;
use Illuminate\Http\Request;
interface PaymentInterface
{
    public function paymentProcess(
        $request,
        $_amount,
        $return,
        $callback
    );
    public function successPayment( Request $request);
    public function calbackPayment( Request $request);
   
    

}
