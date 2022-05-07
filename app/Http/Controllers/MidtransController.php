<?php

namespace App\Http\Controllers;

use App\Models\Midtrans;
use Illuminate\Http\Request;
use App\Services\Midtrans\CreateSnapTokenService;

class MidtransController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaksi = Midtrans::all();
        return view('midtrans.transaksi', compact('transaksi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Models\Midtrans  $midtrans
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaksi = Midtrans::where('id', $id)->first();
        $snapToken = $transaksi->snap_token;
        if (empty($snapToken)) {
            // Jika snap token masih NULL, buat token snap dan simpan ke database

            $midtrans = new CreateSnapTokenService($transaksi);
            $snapToken = $midtrans->getSnapToken();

            $transaksi->snap_token = $snapToken;
            $transaksi->save();
        }

        return view('midtrans.detailTransaksi', compact('transaksi', 'snapToken'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Midtrans  $midtrans
     * @return \Illuminate\Http\Response
     */
    public function edit(Midtrans $midtrans)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Midtrans  $midtrans
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Midtrans $midtrans)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Midtrans  $midtrans
     * @return \Illuminate\Http\Response
     */
    public function destroy(Midtrans $midtrans)
    {
        //
    }
}
