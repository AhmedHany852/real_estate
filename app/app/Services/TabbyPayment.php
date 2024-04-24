<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\PaymentGetway;
use App\Models\PaymentGeteway;
use Illuminate\Support\Facades\Config;
use App\Services\contracts\PaymentInterface;
use Illuminate\Support\Facades\Http;


class TabbyPayment 
{
    public function __construct()
    {

        $tabby = PaymentGetway::where([
            ['keyword', 'Tabby'],
        ])->first();
        $tabbyConf = json_decode($tabby->information, true);
        Config::set('services.tabby.pk_test ',$tabbyConf["pk_test "]);
        Config::set('services.tabby.sk_test  ',$tabbyConf["sk_test "]);
        Config::set('services.tabby.base_url','https://api.tabby.ai/api/v2/');
    }
  
        // $tabby =   Config::get('services.tabby.pk_test');
        // $tabby =   Config::get('services.tabby.sk_test');
    
  

   
    public function createSession($data)
    {
        $body = $this->getConfig($data);

        $http = Http::withToken(Config::get('services.tabby.pk_test'))->baseUrl(Config::get('services.tabby.base_url'));

        $response = $http->post('checkout',$body);

        return $response->object();
    }
    public function getSession($payment_id)
    {
        $http = Http::withToken(Config::get('services.tabby.sk_test'))->baseUrl(Config::get('services.tabby.base_url'));

        $url = 'checkout/'.$payment_id;

        $response = $http->get($url);

        return $response->object();
    }

    public function getConfig($data)
    {
        $body= [];

        $body = [
            "payment" => [
                "amount" => $data['amount'],
                "currency" =>  $data['currency'],
                "description" =>  $data['description'],
                "buyer" => [
                    "phone" => $data['buyer_phone'],
                    "email" => $data['buyer_email'],
                    "name" => $data['full_name'],
                    "dob" => "",
                ],
                "shipping_address" => [
                    "city" => $data['city'],
                    "address" =>  $data['address'],
                    "zip" => $data['zip'],
                ],
                "order" => [
                    "tax_amount" => "0.00",
                    "shipping_amount" => "0.00",
                    "discount_amount" => "0.00",
                    "updated_at" => now(),
                    "reference_id" => $data['order_id'],
                    "items" => 
                        $data['items']
                    ,
                ],
                "buyer_history" => [
                    "registered_since"=> $data['registered_since'],
                    "loyalty_level"=> $data['loyalty_level'],
                ],
            ],
            "lang" => app()->getLocale(),
            "merchant_code" => "your merchant_code",
            "merchant_urls" => [
                "success" => $data['success-url'],
                "cancel" => $data['cancel-url'],
                "failure" => $data['failure-url'],
            ]
        ];

        return $body;
    }
}
