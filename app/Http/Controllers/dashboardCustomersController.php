<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\Hutang;
use App\Models\Mutations;
use Alert;

class dashboardCustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = session()->get('dataLoginCustomers')['id'];
        $username = session()->get('dataLoginCustomers')['username'];
        $customer = Customers::where('id', $id)->get()->first();
        $hutang = Hutang::where('customer_id', $id)->sum('sisa');
        $dataHutang = Hutang::where('customer_id', $id)->get()->sortByDesc('created_at');
        $saldo = $customer['saldo'];
        $point = $customer['point']; 
        $profile = $customer['profile'];
        $mutasiThisDay = Mutations::where('username', $username)->whereDate('created_at', '=', date('Y-m-d'))->get()->sortByDesc('created_at');
        return view('customers.pages.dashboard', compact('saldo', 'point', 'hutang', 'dataHutang', 'profile', 'mutasiThisDay'));
    }

    public function profil()
    {
        $id = session()->get('dataLoginCustomers')['id'];
        $username = session()->get('dataLoginCustomers')['username'];
        $customer = Customers::where('id', $id)->get()->first();
        $hutang = Hutang::where('customer_id', $id)->sum('sisa');
        $dataHutang = Hutang::where('customer_id', $id)->get()->sortByDesc('created_at');
        $saldo = $customer['saldo'];
        $point = $customer['point']; 
        $profile = $customer['profile'];
        $mutasiThisDay = Mutations::where('username', $username)->whereDate('created_at', '=', date('Y-m-d'))->get()->sortByDesc('created_at');
        return view('customers.pages.profil', compact('saldo', 'point', 'hutang', 'dataHutang', 'profile', 'mutasiThisDay'));
    }

    public function changeProfile(Request $request, $id)
    {
            // menyimpan data file yang diupload ke variabel $file
        $file = $request->file('profile');
        
            // nama file
        $namaFile = 'Profile_'.session()->get('dataLoginCustomers')['username'].'.'.$file->getClientOriginalExtension();
        
            // isi dengan nama folder tempat kemana file diupload
        $tujuan_upload = 'lte/dist/img/profile';
        $file->move($tujuan_upload,$namaFile);

            // menyimpan ke db
        $simpanFIleName = Customers::where('id', $id)->first();
        $simpanFIleName->profile = $namaFile;
        $simpanFIleName->save();
        return back()->withInput();
        
    }

    public function tarikCoin()
    {
        $id = session()->get('dataLoginCustomers')['id'];
        $customer = Customers::where('id', $id)->get()->first();
        $point = $customer['point'];
        $customer->saldo = $customer['saldo'] + $point/10;
        
        // mencatat transaksi
        // $transaksi = new Mutations;
        // $transaksi->username = $customer['username'];
        // $transaksi->jenis_transaksi = 'Tarik Coin';
        // $transaksi->code = 'TC'.$customer['point'];
        // $transaksi->status = 'SUCCESS';
        // $transaksi->note = "Penarikan Coin sebesar ".$point." Coin berhasil";
        // $transaksi->save();

        $customer->point = null;
        $customer->save();
        Alert::success('Penarikan Berhasil', 'Uang anda saat ini sebesar Rp. '.$customer['saldo']);
        return redirect('/customers/dashboard');
    }
}
