<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mutations;

class dataTransaksiCustomersController extends Controller
{
    public function index()
    {
    	$username = session()->get('dataLoginCustomers')['username'];
    	$mutasi = Mutations::where('username', $username)->get()->sortByDesc('created_at');
        $mutasiThisDay = Mutations::where('username', $username)->whereDate('created_at', '=', date('Y-m-d'))->get()->sortByDesc('created_at');
        return view('customers.pages.dataTransaksi', compact('mutasi', 'mutasiThisDay'));
    }
}
