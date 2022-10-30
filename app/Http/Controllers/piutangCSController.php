<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hutang;
use App\Models\Customers;

class piutangCSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $TotalHutangCustomer = Hutang::all()->sum('sisa');
        $TotalHutangCustomerLunas = Hutang::where('status', 'Lunas')->sum('nominal');
        $piutang = Hutang::where('status', 'Belum Lunas')->get()->groupBy('customer_id');
        $piutangTerlunaskan = Hutang::where('status', 'Lunas')->get()->groupBy('customer_id');
        $customer = Customers::all();
        return view('cs.pages.piutang', compact('piutang', 'customer', 'piutangTerlunaskan', 'TotalHutangCustomer', 'TotalHutangCustomerLunas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function lunaskanDetail($cusId, $id)
    {
        $hutang = Hutang::where('id', $id)->first();
        $hutang->sisa = 0;
        $hutang->status = 'Lunas';
        $hutang->save();
        return redirect('/cs/piutang/' . $cusId);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $piutang = new Hutang;
        $piutang->customer_id = $request->customer;
        $piutang->nominal = $request->nominal;
        $piutang->sisa = $request->nominal;
        $piutang->keterangan = $request->keterangan;
        $piutang->save();
        return redirect('/cs/piutang');
    }


    public function lunaskan($id)
    {
        $hutang = Hutang::where('customer_id', $id)->update(['status' => 'Lunas', 'sisa' => 0]);
        return redirect('/cs/piutang');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customers::where('id', $id)->first();
        $hutang = Hutang::where('customer_id', $id)->where('status', 'Belum Lunas')->get();
        $hutangTerlunaskan = Hutang::where('customer_id', $id)->where('status', 'Lunas')->get();
        return view('cs.pages.detailPiutang', compact('hutang', 'hutangTerlunaskan', 'customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bayar(Request $request, $cusId, $id)
    {
        $hutang = Hutang::where('id', $id)->first();
        $hutang->sisa = $hutang->sisa - $request->bayar;
        $hutang->save();
        if ($hutang->sisa == 0) {
            $hutang->status = "Lunas";
            $hutang->save();
        }
        return redirect('/cs/piutang/' . $cusId);
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
        $hutang = Hutang::where('id', $id)->first();
        $hutang->nominal = $request->nominal;
        $hutang->sisa = $request->sisa;
        if ($request->sisa == 0) {
            $hutang->status = "Lunas";
        }
        $hutang->keterangan = $request->keterangan;
        $hutang->save();
        return redirect('/cs/piutang/' . $hutang->customer_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($cusId, $id)
    {
        $hutang = Hutang::where('id', $id)->first();
        $hutang->delete();
        return redirect('/cs/piutang/' . $cusId);
    }
}
