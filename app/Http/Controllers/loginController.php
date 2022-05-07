<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\CustomerServices;
use App\Models\Customers;
use App\Mail\sendEmailCustomers;
use Illuminate\Http\Request;
use Alert;

class loginController extends Controller
{
   public function index(Request $request)
   {
      $CustomerService = CustomerServices::where('username', $request->username)->where('deleted', 0)->first();
      $Customer = Customers::where('username', $request->username)->where('deleted', 0)->first();

      if ($CustomerService == true) {
         if ($CustomerService && Hash::check($request->username, $CustomerService->password)) {
            Alert::success('Selamat Datang ' . $CustomerService['name']);
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


            return redirect('/cs/dashboard');
         } else {
            Alert::error('username atau password salah');
            return redirect('/');
         }
      } elseif ($Customer == true) {
         if ($Customer && Hash::check($request->username, $Customer->password)) {
            Alert::success('Selamat Datang ' . $Customer['name']);
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


            return redirect('/customers/dashboard');
         } else {
            Alert::error('username atau password salah');
            return redirect('/');
         }
      } else {
         Alert::error('username atau password salah');
         return redirect('/');
      }
   }



   public function logout()
   {
      Alert::success('Anda Keluar');
      session()->forget('dataLoginCustomerServices');
      session()->forget('dataLoginCustomers');
      return redirect('/');
   }

   public function daftarCustomers(Request $request)
   {
      $name = $request->nama;
      $phone_number = $request->no_hp;
      $random = "CUS" . rand();
      $cus = new Customers;
      $cus->name = $request->nama;
      $cus->username = $random;
      $cus->password = Hash::make($random);
      if (substr(trim($request->no_hp), 0, 2) == '62') {
         $cus->phone_number = '0' . substr(trim($request->no_hp), 2);
      } else {
         $cus->phone_number = $request->no_hp;
      }
      $cus->save();
      // return page
      Alert::success('Buat Akun Berhasil', 'Selamat datang ' . $request->nama);
      // telegram_bot_trx
      $chat_id = "360835825";
      $getUsername = $random;
      $getNama = $request->nama;
      $getNoHP = $request->no_hp;
      $getMessage = "Pendaftar Pengguna Baru";
      $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0ANo Hp : $getNoHP%0A%0A%0A$getMessage";
      $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
      $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_exec($curl);
      curl_close($curl);
      // telegram_bot_trx
      return view('customers.pages.codeMasuk', compact('random', 'name', 'phone_number'));
   }
}
