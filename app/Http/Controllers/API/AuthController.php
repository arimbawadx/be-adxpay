<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\CustomerServices;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use League\OAuth2\Client\Provider\Google;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $CustomerService = CustomerServices::where('username', $request->username)->where('deleted', 0)->first();
        $Customer = Customers::where('username', $request->username)->where('deleted', 0)->first();

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
                    'message' => 'success',
                    'data' => $CustomerService,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'failed',
                    'keterangan' => 'username atau password salah!'
                ], 400);
            }
        } elseif ($Customer) {
            if ($Customer && \Hash::check($request->username, $Customer->password)) {
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
                    'message' => 'success',
                    'data' => $Customer,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'failed',
                    'keterangan' => 'username atau password salah!'
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ]);
        }
    }

    public function test()
    {
        return response()->json([
            'message' => 'ini test'
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

    public function LoginWithGoogle(Request $request)
    {
        try {
            $name = $request->name;
            $email = $request->email;
            $profile = $request->profile;
            $phone_number = '085847801933';

            $CustomerCheck = Customers::where('email', $request->email)->first();
            if (!$CustomerCheck) {
                $random = "CUS" . rand();
                $cus = new Customers;
                $cus->name = $name;
                $cus->username = $random;
                $cus->email = $email;
                $cus->profile = $profile;
                $cus->password = Hash::make($random);
                if (substr(trim($phone_number), 0, 2) == '62') {
                    $cus->phone_number = '0' . substr(trim($phone_number), 2);
                } else {
                    $cus->phone_number = $phone_number;
                }
                $cus->save();

                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = $random;
                $getNama = $name;
                $getNoHP = $phone_number;
                $getEmail = $email;
                $getMessage = "Pendaftar Pengguna Baru";
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0ANo Hp : $getNoHP%0AEmail : $getEmail%0AProfile : $profile%0A%0A%0A$getMessage";
                $tokenTB = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$tokenTB/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            } else {
                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = $CustomerCheck->username;
                $getNama = $CustomerCheck->name;
                $getNoHP = $CustomerCheck->phone_number;
                $getEmail = $CustomerCheck->email;
                $getMessage = "Login Sistem";
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0ANo Hp : $getNoHP%0AEmail : $getEmail%0AProfile : $profile%0A%0A%0A$getMessage";
                $tokenTB = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$tokenTB/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }

            $Customer = Customers::where('email', $request->email)->first();
            return response()->json([
                'status' => 'success',
                'message' => 'Login Berhasil',
                'data' => $Customer,
            ]);
        } catch (Exception $e) {

            // Failed to get user details
            exit('Something went wrong: ' . $e->getMessage());
        }
    }
}
