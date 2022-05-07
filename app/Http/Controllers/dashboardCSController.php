<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Customers;
use App\Models\Hutang;
use App\Models\Mutations;
use Config;

class dashboardCSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        // GET AKUN/CEK SALDO SERPUL
        $curlGetAkunSerpul = curl_init();
        curl_setopt_array($curlGetAkunSerpul, array(
            CURLOPT_URL => "https://api.serpul.co.id/account",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Authorization: '.config('api.serpul_key_api'),
            ),
        ));
        $responseGetAkunSerpul = curl_exec($curlGetAkunSerpul);
        $decodeResponseGetAkunSerpul=json_decode($responseGetAkunSerpul, true);
        $saldoUtama = $decodeResponseGetAkunSerpul;
        $saldo = $saldoUtama['responseData']['balance'];
        // end GET AKUN/CEK SALDO SERPUL
        
        // Get Jumlah trx hari ini
        $curlGetPrabayarHistory = curl_init();
        curl_setopt_array($curlGetPrabayarHistory, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Authorization: '.config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
        $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
        $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData']['data'];
        $trxThisDay = array();
            foreach ($GetPrabayarHistory as $value) {
                if (date('d', strtotime($value['created_at'])) == date('d')) {
                    $trxThisDay[]=$value;
                }
            }
        $jtrxThisDay = count($trxThisDay);
        // end Jumlah trx hari ini


        $TotalsaldoCustomer = Customers::all()->sum('saldo');
        $TotalHutangCustomer = Hutang::all()->sum('sisa');
        $AkumulasiSaldoCS = $saldo - $TotalsaldoCustomer;
        $mutasi = Mutations::whereDate('created_at', '!=', date('Y-m-d'))->get()->sortByDesc('created_at');
        $mutasiThisDay = Mutations::whereDate('created_at', '=', date('Y-m-d'))->get()->sortByDesc('created_at');
        return view('cs.pages.dashboard', compact('saldo', 'TotalsaldoCustomer', 'AkumulasiSaldoCS', 'TotalHutangCustomer', 'mutasi', 'mutasiThisDay', 'jtrxThisDay'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
