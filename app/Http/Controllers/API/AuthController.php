<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\CustomerServices;

class AuthController extends Controller
{
    public function login(Request $request)
    {
       $CustomerService=CustomerServices::where('username', $request->username)->where('deleted', 0)->first();
       $Customer=Customers::where('username', $request->username)->where('deleted', 0)->first();

       if ($CustomerService) {
          if ($CustomerService && \Hash::check($request->username, $CustomerService->password)) {
           session()->put('dataLoginCustomerServices', $CustomerService);

         // telegram_bot_trx
           $chat_id = "360835825";
           $getUsername = session()->get('dataLoginCustomerServices')['username'];
           $getNama = session()->get('dataLoginCustomerServices')['name'];
           $getMessage = "Login Sistem";
           $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
           $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
           $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
           $curl = curl_init($url);
           curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
           curl_exec($curl);
           curl_close($curl);
        // telegram_bot_trx

           return response()->json([
            'message'=> 'success',
            'data'=> $CustomerService,
        ], 200);
       }else{
           return response()->json([
            'message'=> 'failed',
            'keterangan'=>'username atau password salah!'
        ], 400);
       }
   }elseif($Customer){
    if($Customer && \Hash::check($request->username, $Customer->password)){
       session()->put('dataLoginCustomers', $Customer);

         // telegram_bot_trx
       $chat_id = "360835825";
       $getUsername = session()->get('dataLoginCustomers')['username'];
       $getNama = session()->get('dataLoginCustomers')['name'];
       $getMessage = "Login Sistem";
       $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
       $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
       $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
       $curl = curl_init($url);
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
       curl_exec($curl);
       curl_close($curl);
        // telegram_bot_trx


       return response()->json([
        'message'=> 'success',
        'data'=> $Customer,
    ], 200);
   }else{
       return response()->json([
        'message'=> 'failed',
        'keterangan'=>'username atau password salah!'
    ], 400);
   }
}else{
    return response()->json([
        'message'=>'Unauthorized'
    ]);
}
}

public function test()
{
   return response()->json([
    'message'=>'ini test'
], 200);
}

public function logout(Request $request)
{
    session()->forget('dataLoginCustomerServices');
    session()->forget('dataLoginCustomers');
    return response()->json([
        'message' => 'Berhasil Logout'
    ], 200);
}
}
