<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboardCSController;
use App\Http\Controllers\dashboardCustomersController;
use App\Http\Controllers\dataCSCSController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\dataCustomerCSController;
use App\Http\Controllers\transaksiCSController;
use App\Http\Controllers\transaksiCustomerController;
use App\Http\Controllers\dataTransaksiCustomersController;
use App\Http\Controllers\isiDompetCustomersController;
use App\Http\Controllers\isiDompetCSController;
use App\Http\Controllers\piutangCSController;
use App\Http\Controllers\briApiCSController;
use App\Http\Controllers\apiSerpulController;
use App\Http\Controllers\MidtransController;


// =====================login============================
Route::get('/midtrans', [MidtransController::class, 'index']);
Route::get('/midtrans/{id}', [MidtransController::class, 'show']);

Route::get('/adxpay', function () {
	return view('landing_page');
});
Route::get('/', function () {
	return view('login');
});
Route::post('/login-check', [loginController::class, 'index']);
Route::get('/logout', [loginController::class, 'logout']);
// ======================end login===========================



// =======================Customer Service===========================
// dashboard
Route::get('/cs/dashboard', [dashboardCSController::class, 'index'])->middleware('SessionCustomerServices');

// data cs
Route::get('/cs/users/data-cs', [dataCSCSController::class, 'index'])->middleware('SessionCustomerServices');
Route::post('/cs/users/data-cs', [dataCSCSController::class, 'store'])->middleware('SessionCustomerServices');
Route::post('/cs/users/data-cs/update/{id}', [dataCSCSController::class, 'update'])->middleware('SessionCustomerServices');
Route::get('/cs/users/data-cs/delete/{id}', [dataCSCSController::class, 'destroy'])->middleware('SessionCustomerServices');
Route::get('/cs/users/data-cs/restore/{id}', [dataCSCSController::class, 'restore'])->middleware('SessionCustomerServices');

// data Customer
Route::get('/cs/users/data-customers', [dataCustomerCSController::class, 'index'])->middleware('SessionCustomerServices');
Route::post('/cs/users/data-customers', [dataCustomerCSController::class, 'store'])->middleware('SessionCustomerServices');
Route::post('/cs/users/data-customers/update/{id}', [dataCustomerCSController::class, 'update'])->middleware('SessionCustomerServices');
Route::post('/cs/users/data-customers/deposit/{id}', [dataCustomerCSController::class, 'deposit'])->middleware('SessionCustomerServices');
Route::get('/cs/users/data-customers/delete/{id}', [dataCustomerCSController::class, 'destroy'])->middleware('SessionCustomerServices');
Route::get('/cs/users/data-customers/restore/{id}', [dataCustomerCSController::class, 'restore'])->middleware('SessionCustomerServices');


// menunggu konfirmasi
Route::get('/cs/transaksi-deposit', [isiDompetCSController::class, 'transaksiDeposit'])->middleware('SessionCustomerServices');

// validTransfer
Route::get('/cs/isi-dompet/valid/{id}', [isiDompetCSController::class, 'validTransfer'])->middleware('SessionCustomerServices');

// invalid Tranfer
Route::get('/cs/isi-dompet/invalid/{id}', [isiDompetCSController::class, 'invalidTransfer'])->middleware('SessionCustomerServices');

// Piutang
Route::get('/cs/piutang', [piutangCSController::class, 'index'])->middleware('SessionCustomerServices');
Route::get('/cs/piutang/{id}', [piutangCSController::class, 'show'])->middleware('SessionCustomerServices');
Route::post('/cs/piutang/ubah/{id}', [piutangCSController::class, 'update'])->middleware('SessionCustomerServices');
Route::post('/cs/piutang/bayar/{cusId}/{id}', [piutangCSController::class, 'bayar'])->middleware('SessionCustomerServices');
Route::get('/cs/piutang/hapus/{cusId}/{id}', [piutangCSController::class, 'destroy'])->middleware('SessionCustomerServices');
Route::post('/cs/piutang/tambah', [piutangCSController::class, 'store'])->middleware('SessionCustomerServices');
Route::get('/cs/piutang/lunaskan/{id}', [piutangCSController::class, 'lunaskan'])->middleware('SessionCustomerServices');
Route::get('/cs/piutang/lunaskan/{cusId}/{id}', [piutangCSController::class, 'lunaskanDetail'])->middleware('SessionCustomerServices');

// BRIAPI
Route::get('/cs/info-saldo-rek-bri', [briApiCSController::class, 'CekSaldo']);
Route::get('/cs/riwayat-trx-rek-bri', [briApiCSController::class, 'RiwayatTransaksi']);


// SerpulTransaksi
// update Pending Trx
Route::get('/cs/transaksi/update/{id}', [transaksiCSController::class, 'UpdatePendingTrx'])->middleware('SessionCustomerServices');
// bukti trx global
Route::get('/cs/transaksi/{trxid_api}', [transaksiCSController::class, 'transaksiBukti'])->middleware('SessionCustomerServices');
// pulsa
Route::get('/cs/transaksi/PIU/1', [transaksiCSController::class, 'transaksiPIU1'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/PIU/2', [transaksiCSController::class, 'transaksiPIU2'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/PIU/3', [transaksiCSController::class, 'transaksiPIU3'])->middleware('SessionCustomerServices');
Route::get('/cs/transaksi/PIU/{trxid_api}', [transaksiCSController::class, 'transaksiPIUBukti'])->middleware('SessionCustomerServices');


// bankTransefer
Route::get('/cs/transaksi/Bank/1', [transaksiCSController::class, 'transaksiBank1'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/Bank/2', [transaksiCSController::class, 'transaksiBank2'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/Bank/3', [transaksiCSController::class, 'transaksiBank3'])->middleware('SessionCustomerServices');
Route::get('/cs/transaksi/Bank/{trxid_api}', [transaksiCSController::class, 'transaksiBankBukti'])->middleware('SessionCustomerServices');


// Paket Data Internet
Route::get('/cs/transaksi/PD/1', [transaksiCSController::class, 'transaksiPD1'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/PD/2', [transaksiCSController::class, 'transaksiPD2'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/PD/3', [transaksiCSController::class, 'transaksiPD3'])->middleware('SessionCustomerServices');
Route::get('/cs/transaksi/PD/{trxid_api}', [transaksiCSController::class, 'transaksiPDBukti'])->middleware('SessionCustomerServices');

// Token Listrik
Route::get('/cs/transaksi/TL/1', [transaksiCSController::class, 'transaksiTL1'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/TL/2', [transaksiCSController::class, 'transaksiTL2'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/TL/3', [transaksiCSController::class, 'transaksiTL3'])->middleware('SessionCustomerServices');
Route::get('/cs/transaksi/TL/{trxid_api}', [transaksiCSController::class, 'transaksiTLBukti'])->middleware('SessionCustomerServices');

// Topup Dana
Route::get('/cs/transaksi/DANA/1', [transaksiCSController::class, 'transaksiDANA1'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/DANA/2', [transaksiCSController::class, 'transaksiDANA2'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/DANA/3', [transaksiCSController::class, 'transaksiDANA3'])->middleware('SessionCustomerServices');

// Topup OVO
Route::get('/cs/transaksi/OVO/1', [transaksiCSController::class, 'transaksiOVO1'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/OVO/2', [transaksiCSController::class, 'transaksiOVO2'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/OVO/3', [transaksiCSController::class, 'transaksiOVO3'])->middleware('SessionCustomerServices');

// Topup ShopeePay
Route::get('/cs/transaksi/ShopeePay/1', [transaksiCSController::class, 'transaksiShopeePay1'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/ShopeePay/2', [transaksiCSController::class, 'transaksiShopeePay2'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/ShopeePay/3', [transaksiCSController::class, 'transaksiShopeePay3'])->middleware('SessionCustomerServices');

// Topup GoPay
Route::get('/cs/transaksi/GoPay/1', [transaksiCSController::class, 'transaksiGoPay1'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/GoPay/2', [transaksiCSController::class, 'transaksiGoPay2'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/GoPay/3', [transaksiCSController::class, 'transaksiGoPay3'])->middleware('SessionCustomerServices');

// Topup LinkAja
Route::get('/cs/transaksi/LinkAja/1', [transaksiCSController::class, 'transaksiLinkAja1'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/LinkAja/2', [transaksiCSController::class, 'transaksiLinkAja2'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/LinkAja/3', [transaksiCSController::class, 'transaksiLinkAja3'])->middleware('SessionCustomerServices');

// Topup WifiId
Route::get('/cs/transaksi/wifi-id/1', [transaksiCSController::class, 'transaksiWifiId1'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/wifi-id/2', [transaksiCSController::class, 'transaksiWifiId2'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/wifi-id/3', [transaksiCSController::class, 'transaksiWifiId3'])->middleware('SessionCustomerServices');

// VGML
Route::get('/cs/transaksi/vgml/2', [transaksiCSController::class, 'transaksivgml2'])->middleware('SessionCustomerServices');
Route::post('/cs/transaksi/vgml/3', [transaksiCSController::class, 'transaksivgml3'])->middleware('SessionCustomerServices');

// VGFF
Route::get('/cs/transaksi/vgff/2', [transaksiCSController::class, 'transaksivgff2'])->middleware('SessionCustomers');
Route::post('/cs/transaksi/vgff/3', [transaksiCSController::class, 'transaksivgff3'])->middleware('SessionCustomers');
// =======================end Customer Service===========================



// =======================Customer===========================
// daftar
Route::get('/customers/daftar', function () {
	return view('customers.pages.daftar');
});
Route::post('/customers/registering', [loginController::class, 'daftarCustomers']);

// dashboard
Route::get('/customers/dashboard', [dashboardCustomersController::class, 'index'])->middleware('SessionCustomers');
Route::get('/customers/profil', [dashboardCustomersController::class, 'profil'])->middleware('SessionCustomers');
Route::post('/customers/ganti-profile/{id}', [dashboardCustomersController::class, 'changeProfile'])->middleware('SessionCustomers');

// tarik-coin
Route::post('/customers/tarik-coin', [dashboardCustomersController::class, 'tarikCoin'])->middleware('SessionCustomers');

// data-transaksi
Route::get('/customers/data-transaksi', [dataTransaksiCustomersController::class, 'index'])->middleware('SessionCustomers');

// isi dompet
Route::get('/customers/transaksi/isi-dompet', [isiDompetCustomersController::class, 'index'])->middleware('SessionCustomers');
Route::post('/customers/transaksi/isi-dompet/proses', [isiDompetCustomersController::class, 'isiDompet'])->middleware('SessionCustomers');
Route::post('/customers/transaksi/isi-dompet/upload-bukti/{id}', [isiDompetCustomersController::class, 'uploadBuktiTransfer'])->middleware('SessionCustomers');

// SerpulTransaksi
// update Pending Trx
Route::get('/cus/transaksi/update/{id}', [transaksiCustomerController::class, 'UpdatePendingTrx'])->middleware('SessionCustomers');
// bukti trx global
Route::get('/cus/transaksi/{trxid_api}', [transaksiCustomerController::class, 'transaksiBukti'])->middleware('SessionCustomers');
// pulsa
Route::get('/cus/transaksi/PIU/1', [transaksiCustomerController::class, 'transaksiPIU1'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/PIU/2', [transaksiCustomerController::class, 'transaksiPIU2'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/PIU/3', [transaksiCustomerController::class, 'transaksiPIU3'])->middleware('SessionCustomers');

// bankTransefer
Route::get('/cus/transaksi/Bank/1', [transaksiCustomerController::class, 'transaksiBank1'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/Bank/2', [transaksiCustomerController::class, 'transaksiBank2'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/Bank/3', [transaksiCustomerController::class, 'transaksiBank3'])->middleware('SessionCustomers');

// Paket Data Internet
Route::get('/cus/transaksi/PD/1', [transaksiCustomerController::class, 'transaksiPD1'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/PD/2', [transaksiCustomerController::class, 'transaksiPD2'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/PD/3', [transaksiCustomerController::class, 'transaksiPD3'])->middleware('SessionCustomers');

// Token Listrik
Route::get('/cus/transaksi/TL/1', [transaksiCustomerController::class, 'transaksiTL1'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/TL/2', [transaksiCustomerController::class, 'transaksiTL2'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/TL/3', [transaksiCustomerController::class, 'transaksiTL3'])->middleware('SessionCustomers');
Route::get('/cus/transaksi/TL/{trxid_api}', [transaksiCustomerController::class, 'transaksiTLBukti'])->middleware('SessionCustomers');

// Topup Dana
Route::get('/cus/transaksi/DANA/1', [transaksiCustomerController::class, 'transaksiDANA1'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/DANA/2', [transaksiCustomerController::class, 'transaksiDANA2'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/DANA/3', [transaksiCustomerController::class, 'transaksiDANA3'])->middleware('SessionCustomers');

// Topup OVO
Route::get('/cus/transaksi/OVO/1', [transaksiCustomerController::class, 'transaksiOVO1'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/OVO/2', [transaksiCustomerController::class, 'transaksiOVO2'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/OVO/3', [transaksiCustomerController::class, 'transaksiOVO3'])->middleware('SessionCustomers');

// Topup ShopeePay
Route::get('/cus/transaksi/ShopeePay/1', [transaksiCustomerController::class, 'transaksiShopeePay1'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/ShopeePay/2', [transaksiCustomerController::class, 'transaksiShopeePay2'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/ShopeePay/3', [transaksiCustomerController::class, 'transaksiShopeePay3'])->middleware('SessionCustomers');

// Topup GoPay
Route::get('/cus/transaksi/GoPay/1', [transaksiCustomerController::class, 'transaksiGoPay1'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/GoPay/2', [transaksiCustomerController::class, 'transaksiGoPay2'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/GoPay/3', [transaksiCustomerController::class, 'transaksiGoPay3'])->middleware('SessionCustomers');

// Topup LinkAja
Route::get('/cus/transaksi/LinkAja/1', [transaksiCustomerController::class, 'transaksiLinkAja1'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/LinkAja/2', [transaksiCustomerController::class, 'transaksiLinkAja2'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/LinkAja/3', [transaksiCustomerController::class, 'transaksiLinkAja3'])->middleware('SessionCustomers');

// Topup WifiId
Route::get('/cus/transaksi/wifi-id/1', [transaksiCustomerController::class, 'transaksiWifiId1'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/wifi-id/2', [transaksiCustomerController::class, 'transaksiWifiId2'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/wifi-id/3', [transaksiCustomerController::class, 'transaksiWifiId3'])->middleware('SessionCustomers');

// VGML
Route::get('/cus/transaksi/vgml/2', [transaksiCustomerController::class, 'transaksivgml2'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/vgml/3', [transaksiCustomerController::class, 'transaksivgml3'])->middleware('SessionCustomers');

// VGFF
Route::get('/cus/transaksi/vgff/2', [transaksiCustomerController::class, 'transaksivgff2'])->middleware('SessionCustomers');
Route::post('/cus/transaksi/vgff/3', [transaksiCustomerController::class, 'transaksivgff3'])->middleware('SessionCustomers');
// =======================end Customer===========================



Route::get('/clear', function () {
	Artisan::call('cache:clear');
	Artisan::call('route:cache');
	Artisan::call('view:clear');
	Artisan::call('config:cache');
	dd("Cache Clear All");
});

// =======================Serpul API===========================
// prabayar
Route::get('/serpul/get-akun', [apiSerpulController::class, 'GetAkunSerpul']);
Route::get('/serpul/get-prabayar-kategori', [apiSerpulController::class, 'GetPrabayarCategory']);
Route::get('/serpul/get-prabayar-operator', [apiSerpulController::class, 'GetPrabayarOperator']);
Route::get('/serpul/get-prabayar-produk', [apiSerpulController::class, 'GetPrabayarProduct']);
Route::get('/serpul/post-prabayar', [apiSerpulController::class, 'PostPrabayar']);
Route::get('/serpul/get-prabayar-history', [apiSerpulController::class, 'GetPrabayarHistory']);
// pascabayar
Route::get('/serpul/get-pascabayar-kategori', [apiSerpulController::class, 'GetPascabayarCategory']);
Route::get('/serpul/get-pascabayar-produk', [apiSerpulController::class, 'GetPascabayarProduct']);
Route::get('/serpul/post-pascabayar', [apiSerpulController::class, 'PostPascabayar']);
Route::get('/serpul/get-pascabayar-history', [apiSerpulController::class, 'GetPascabayarHistory']);
// =======================End Serpul API===========================


Route::get('/ip', function () {
	// $tast = geoip()->getLocation('192.168.3.112');
	$checkLoc = geoip()->getLocation($_SERVER['REMOTE_ADDR']);
	return $checkLoc->toArray();
});

Route::get('/login-with-google', [AuthController::class, 'LoginWithGoogle']);
