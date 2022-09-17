<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\dashboardCustomersController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware(['APISessionCustomers'])->group(function () {
    Route::post('/customers/ganti-profile/{id}', [dashboardCustomersController::class, 'changeProfile']);
    Route::get('/get-data-cus/{id}', [dashboardCustomersController::class, 'getDataCustomer']);
    Route::get('/get-data-trx-cus', [dashboardCustomersController::class, 'getDataTrxCustomer']);
    Route::get('/get-bukti-trx-cus/{id}', [dashboardCustomersController::class, 'getBuktiTrxCustomer']);
    Route::get('/get-prabayar-produk/{id}', [dashboardCustomersController::class, 'GetPrabayarProduct']);
    Route::get('/get-prabayar-operator/{id}', [dashboardCustomersController::class, 'GetPrabayarOperator']);
    Route::get('/get-data-hutang', [dashboardCustomersController::class, 'getDataHutang']);
    Route::post('/transaksi/tarik-coin', [dashboardCustomersController::class, 'tarikCoin']);
    Route::get('/transaksi/update/{id}', [dashboardCustomersController::class, 'UpdatePendingTrx']);
    Route::get('/get-data-jenis-bank', [dashboardCustomersController::class, 'getDataJenisBank']);
    Route::post('/transaksi/piu', [dashboardCustomersController::class, 'transaksiPIU3']);
    Route::post('/transaksi/pd', [dashboardCustomersController::class, 'transaksiPD3']);
    Route::post('/transaksi/tl', [dashboardCustomersController::class, 'transaksiTL3']);
    Route::post('/transaksi/tb', [dashboardCustomersController::class, 'transaksiTB3']);
    Route::post('/transaksi/dana', [dashboardCustomersController::class, 'transaksiDANA3']);
    Route::post('/transaksi/ovo', [dashboardCustomersController::class, 'transaksiOVO3']);
    Route::post('/transaksi/sp', [dashboardCustomersController::class, 'transaksiShopeePay3']);
    Route::post('/transaksi/gp', [dashboardCustomersController::class, 'transaksiGoPay3']);
    Route::post('/transaksi/la', [dashboardCustomersController::class, 'transaksiLinkAja3']);
    Route::post('/transaksi/wi', [dashboardCustomersController::class, 'transaksiWifiId3']);
    Route::post('/transaksi/vgml', [dashboardCustomersController::class, 'transaksivgml3']);
    Route::post('/transaksi/vgff', [dashboardCustomersController::class, 'transaksivgff3']);
    Route::post('/check-tagihan-pln/{id}', [dashboardCustomersController::class, 'CheckTagihanPLN']);
    Route::post('/transaksi/tlpasca', [dashboardCustomersController::class, 'transaksiTLPasca']);
});

Route::middleware(['APISessionCustomerServices'])->group(function () {
    //req cs
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/daftar-cus', [dashboardCustomersController::class, 'daftarCustomers']);
Route::post('/new-cus-gakun', [AuthController::class, 'LoginWithGoogle']);
