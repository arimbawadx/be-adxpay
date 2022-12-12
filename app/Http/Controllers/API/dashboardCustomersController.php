<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\Mutations;
use App\Models\Hutang;
use Illuminate\Support\Facades\Hash;

class dashboardCustomersController extends Controller
{
    public function changeProfile(Request $request, $id)
    {
        // menyimpan data file yang diupload ke variabel $file
        $file = $request->photoProfile;
        $username = $request->username;

        // nama file
        $namaFile = 'Profile_' . $username . date('YmdHis') . '.' . $file->getClientOriginalExtension();

        // isi dengan nama folder tempat kemana file diupload
        $tujuan_upload = 'lte/dist/img/profile';
        $file->move($tujuan_upload, $namaFile);

        // menyimpan ke db
        $simpanFIleName = Customers::where('id', $id)->first();
        $simpanFIleName->profile = $namaFile;
        $simpanFIleName->save();
        return response()->json([
            'message' => 'success',
            $request->all(),
        ], 200);
    }

    public function getDataCustomer($id)
    {
        $cus = Customers::where('id', $id)->first();
        if ($cus) {
            return response()->json([
                'message' => 'success',
                'data' => $cus,
            ], 200);
        } else {
            return response()->json([
                'message' => 'failed',
                'keterangan' => 'Data tidak ditemukan!',
            ], 200);
        }
    }

    public function getDataTrxCustomer(Request $request)
    {
        $username = $request->username;
        $mutasi = Mutations::where('username', $username)->where('status', '!=', 'KADALUARSA')->whereDate('created_at', '!=', date('Y-m-d'))->get()->sortByDesc('id');
        $mutasiThisDay = Mutations::where('username', $username)->whereDate('created_at', '=', date('Y-m-d'))->get()->sortByDesc('id');
        return response()->json([
            'status' => 'success',
            'mutasi_hari_ini' => $mutasiThisDay,
            'semua_mutasi_sebelumnya' => $mutasi,
        ], 200);
    }

    public function getBuktiTrxCustomer(Request $request, $id)
    {
        // Get status Trx 
        $curlGetPrabayarHistory = curl_init();
        curl_setopt_array($curlGetPrabayarHistory, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Authorization: ' . config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
        $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
        $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
        // return $GetPrabayarHistory;
        // end Get status Trx 

        if ($GetPrabayarHistory == null) {
            // perubahan mutasi
            $mutasi = Mutations::where('trxid_api', $id)->get()->first();
            $mutasi->status = "KADALUARSA";
            $mutasi->save();
        }

        return response()->json([
            'status' => 'success',
            'data' => $GetPrabayarHistory,
        ], 200);
    }

    public function GetPrabayarProduct($id)
    {
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Authorization: ' . config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
        return $GetPrabayarProduct;
        // $PrabayarProduct = $GetPrabayarProduct['responseData']['balance'];
        // end Get Prabayar Product
    }

    public function GetPrabayarOperator($id)
    {
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/operator?product_id=$id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Authorization: ' . config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
        return $GetPrabayarProduct;
        // $PrabayarProduct = $GetPrabayarProduct['responseData']['balance'];
        // end Get Prabayar Product
    }

    public function getDataHutang(Request $request)
    {
        $idCustomer = Customers::where('username', $request->username)->first()->id;
        $dataHutang = Hutang::where('customer_id', $idCustomer)->get();
        $totalHutang = Hutang::where('customer_id', $idCustomer)->sum('sisa');

        return response()->json([
            'data_hutang' => $dataHutang,
            'total_hutang' => $totalHutang,
        ], 200);
    }

    public function tarikCoin(Request $request)
    {
        $username = $request->username;
        $customer = Customers::where('username', $username)->get()->first();
        $point = $customer['point'];
        $customer->saldo = $customer['saldo'] + $point / 10;

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
        return response()->json([
            'status' => 'success',
            'message' => 'Penarikan Berhasil',
            'keterangan' => 'Saldo anda saat ini sebesar Rp. ' . $customer['saldo'],
        ], 200);
    }

    public function UpdatePendingTrx(Request $request, $id)
    {
        // Get status Trx 
        $curlGetPrabayarHistory = curl_init();
        curl_setopt_array($curlGetPrabayarHistory, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Authorization: ' . config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
        $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
        $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
        // return $GetPrabayarHistory;
        // end Get status Trx 

        if ($GetPrabayarHistory == null) {
            // perubahan mutasi
            $mutasi = Mutations::where('trxid_api', $id)->get()->first();
            $mutasi->status = "KADALUARSA";
            $mutasi->save();
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Lama',
                'keterangan' => 'Transaksi lama akan dihapus otomatis, simpan data transaksi Anda!',
            ], 200);

            // telegram_bot_trx
            $chat_id = "360835825";
            $cus = Customers::where('username', $request->username)->first();
            $getUsername = $cus->username;
            $getNama = $cus->name;
            $getMessage = "Refresh trx Lama";
            $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
            $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
            $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_exec($curl);
            curl_close($curl);
            // telegram_bot_trx
        } else {
            if ($GetPrabayarHistory['status'] != "PENDING") {
                // perubahan mutasi
                $mutasi = Mutations::where('trxid_api', $id)->get()->first();
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->note = $GetPrabayarHistory['product_name'];
                $mutasi->save();
                if ($GetPrabayarHistory['status'] == "FAILED") {
                    // menghapus hutang
                    $hutang = Hutang::where('trxid_api', $id)->get()->first();
                    if ($hutang != null) {
                        $hutang->delete();
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'Transaksi Gagal',
                            'keterangan' => 'Terjadi gangguan, hubungi admin',
                        ], 200);
                    } else {
                        $historyHarga = $mutasi->harga_jual;
                        $username = $request->username;
                        // update saldo customer
                        $UpdateSaldoCustomer = Customers::where('username', $username)->first();
                        $UpdateSaldoCustomer->saldo = $UpdateSaldoCustomer->saldo + $historyHarga;
                        $UpdateSaldoCustomer->save();
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'Transaksi Gagal',
                            'keterangan' => 'Terjadi gangguan, hubungi admin',
                        ], 200);
                    }

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = "Gagal";
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // menambah point
                    $username = $request->username;
                    $UpdatePointCustomer = Customers::where('username', $username)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1000',
                    ], 200);

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = "Sukses";
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                }
            } elseif ($GetPrabayarHistory['status'] == "PENDING") {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaksi Sedang Diproses',
                    'keterangan' => 'Transaksi diproses, silahkan menunggu',
                ], 200);

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = "Pending";
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        }
    }

    public function getDataJenisBank(Request $request)
    {
        // Get Prabayar Operator
        $curlGetPrabayarOperator = curl_init();
        curl_setopt_array($curlGetPrabayarOperator, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/operator?product_id=TB",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Authorization: ' . config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarOperator = curl_exec($curlGetPrabayarOperator);
        $decodeResponseGetPrabayarOperator = json_decode($responseGetPrabayarOperator, true);
        $GetPrabayarOperator = $decodeResponseGetPrabayarOperator;
        return $GetPrabayarOperator['responseData'];
    }

    public function transaksiPIU3(Request $request)
    {
        // return $request->all();

        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $hutangSaatIni = Hutang::where('customer_id', $request->cus_id)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak',
                    'keterangan' => 'Hutang Anda Sudah Banyak, Bayar Dulu !',
                ], 200);
            }
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination" => $request->no_hp,
                "product_id" => $request->produk,
                "ref_id" => $trxid_api,
            ];
            $curlPostPrabayar = curl_init();
            curl_setopt_array($curlPostPrabayar, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dataPostPrabayar,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Terjadi Gangguan',
                    'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                ], 200);
            } else { //success inquiry

                // Get status Trx 
                $curlGetPrabayarHistory = curl_init();
                curl_setopt_array($curlGetPrabayarHistory, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi->username = $request->username;
                $mutasi->jenis_transaksi = 'Pulsa Isi Ulang';
                $mutasi->code = $request->produk;
                $mutasi->phone = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                $mutasi->note = "Pembelian Pulsa Isi Ulang " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk Pulsa Isi Ulang " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk Pulsa Isi Ulang " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = $request->cus_id;
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1.000',
                    ], 200);
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = $mutasi->note . " " . $request->no_hp;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        } elseif ($request->metode_pembayaran == "Dompet") {
            $operatorProduk = $request->operatorProduk;
            $idProduk = $request->produk;
            // cekHargaProdukYangDibeli
            // Get Prabayar Product
            $curlGetPrabayarProduct = curl_init();
            curl_setopt_array($curlGetPrabayarProduct, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$operatorProduk",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
            $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
            $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
            // cekHargaProdukIni
            $produkIni = array();
            foreach ($GetPrabayarProduct as $value) {
                if ($value['product_id'] == $idProduk) {
                    $produkIni[] = $value;
                }
            }
            $harga = preg_replace('/[^0-9]/', '', $produkIni[0]['product_name']) + 2000;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = $request->cus_id;
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {


                // melanjutkan trx
                // membuat trxid_api
                $trxid_api = date('Ymdhis');

                // Post Prabayar
                $dataPostPrabayar = [
                    "destination" => $request->no_hp,
                    "product_id" => $request->produk,
                    "ref_id" => $trxid_api,
                ];
                $curlPostPrabayar = curl_init();
                curl_setopt_array($curlPostPrabayar, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $dataPostPrabayar,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responsePostPrabayar = curl_exec($curlPostPrabayar);
                $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
                // end Post Prabayar
                if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Terjadi Gangguan',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                } else { //success inquiry

                    $saldoNew = $dompetSekarang - $harga;
                    $Customer->saldo = $saldoNew;
                    $Customer->save();
                    // end pengurangan dompet

                    // Get status Trx 
                    $curlGetPrabayarHistory = curl_init();
                    curl_setopt_array($curlGetPrabayarHistory, array(
                        CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Authorization: ' . config('api.serpul_key_api'),
                        ),
                    ));
                    $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                    $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                    $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                    // end Get status Trx 

                    // mencatat ke mutasi
                    $mutasi = new Mutations;
                    $mutasi->username = $request->username;
                    $mutasi->jenis_transaksi = 'Pulsa Isi Ulang';
                    $mutasi->code = $request->produk;
                    $mutasi->phone = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $mutasi->note = "Pembelian Pulsa Isi Ulang " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = $mutasi->note . " " . $request->no_hp;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi diproses',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                }
            } elseif ($dompetSekarang < $harga) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak!',
                    'keterangan' => 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang),
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Ditolak!',
                'keterangan' => 'metode pembayaran tidak valid'
            ], 200);
        }
    }

    public function transaksiPD3(Request $request)
    {
        // return $request->all();

        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $hutangSaatIni = Hutang::where('customer_id', $request->cus_id)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak',
                    'keterangan' => 'Hutang Anda Sudah Banyak, Bayar Dulu !',
                ], 200);
            }
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination" => $request->no_hp,
                "product_id" => $request->produk,
                "ref_id" => $trxid_api,
            ];
            $curlPostPrabayar = curl_init();
            curl_setopt_array($curlPostPrabayar, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dataPostPrabayar,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Terjadi Gangguan',
                    'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                ], 200);
            } else { //success inquiry

                // Get status Trx 
                $curlGetPrabayarHistory = curl_init();
                curl_setopt_array($curlGetPrabayarHistory, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi->username = $request->username;
                $mutasi->jenis_transaksi = 'Paket Data Internet';
                $mutasi->code = $request->produk;
                $mutasi->phone = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = $GetPrabayarHistory['price'] + 5000;
                $mutasi->note = "Pembelian Paket Data Internet | " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 5000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Paket Data Internet | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 5000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Paket Data Internet | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = $request->cus_id;
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1.000',
                    ], 200);
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = $mutasi->note . " " . $request->no_hp;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        } elseif ($request->metode_pembayaran == "Dompet") {
            $operatorProduk = $request->operatorProduk;
            $idProduk = $request->produk;
            // cekHargaProdukYangDibeli
            // Get Prabayar Product
            $curlGetPrabayarProduct = curl_init();
            curl_setopt_array($curlGetPrabayarProduct, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$operatorProduk",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
            $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
            $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
            // cekHargaProdukIni
            $produkIni = array();
            foreach ($GetPrabayarProduct as $value) {
                if ($value['product_id'] == $idProduk) {
                    $produkIni[] = $value;
                }
            }
            $harga = $produkIni[0]['product_price'] + 5000;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = $request->cus_id;
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {


                // melanjutkan trx
                // membuat trxid_api
                $trxid_api = date('Ymdhis');

                // Post Prabayar
                $dataPostPrabayar = [
                    "destination" => $request->no_hp,
                    "product_id" => $request->produk,
                    "ref_id" => $trxid_api,
                ];
                $curlPostPrabayar = curl_init();
                curl_setopt_array($curlPostPrabayar, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $dataPostPrabayar,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responsePostPrabayar = curl_exec($curlPostPrabayar);
                $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
                // end Post Prabayar
                if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Terjadi Gangguan',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                } else { //success inquiry

                    $saldoNew = $dompetSekarang - $harga;
                    $Customer->saldo = $saldoNew;
                    $Customer->save();
                    // end pengurangan dompet

                    // Get status Trx 
                    $curlGetPrabayarHistory = curl_init();
                    curl_setopt_array($curlGetPrabayarHistory, array(
                        CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Authorization: ' . config('api.serpul_key_api'),
                        ),
                    ));
                    $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                    $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                    $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                    // end Get status Trx 

                    // mencatat ke mutasi
                    $mutasi = new Mutations;
                    $mutasi->username = $request->username;
                    $mutasi->jenis_transaksi = 'Paket Data Internet';
                    $mutasi->code = $request->produk;
                    $mutasi->phone = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = $GetPrabayarHistory['price'] + 5000;
                    $mutasi->note = "Pembelian Paket Data Internet | " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = $mutasi->note . " " . $request->no_hp;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi diproses',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                }
            } elseif ($dompetSekarang < $harga) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak!',
                    'keterangan' => 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang),
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Ditolak!',
                'keterangan' => 'metode pembayaran tidak valid'
            ], 200);
        }
    }

    public function transaksiTL3(Request $request)
    {
        // return $request->all();

        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $hutangSaatIni = Hutang::where('customer_id', $request->cus_id)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak',
                    'keterangan' => 'Hutang Anda Sudah Banyak, Bayar Dulu !',
                ], 200);
            }
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination" => $request->no_hp,
                "product_id" => $request->produk,
                "ref_id" => $trxid_api,
            ];
            $curlPostPrabayar = curl_init();
            curl_setopt_array($curlPostPrabayar, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dataPostPrabayar,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Terjadi Gangguan',
                    'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                ], 200);
            } else { //success inquiry

                // Get status Trx 
                $curlGetPrabayarHistory = curl_init();
                curl_setopt_array($curlGetPrabayarHistory, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi->username = $request->username;
                $mutasi->jenis_transaksi = 'Token Listrik';
                $mutasi->code = $request->produk;
                $mutasi->phone = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                $mutasi->note = "Pembelian Token Listrik | " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Token Listrik | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Token Listrik | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = $request->cus_id;
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1.000',
                    ], 200);
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        } elseif ($request->metode_pembayaran == "Dompet") {
            $operatorProduk = $request->operatorProduk;
            $idProduk = $request->produk;
            // cekHargaProdukYangDibeli
            // Get Prabayar Product
            $curlGetPrabayarProduct = curl_init();
            curl_setopt_array($curlGetPrabayarProduct, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$operatorProduk",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
            $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
            $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
            // cekHargaProdukIni
            $produkIni = array();
            foreach ($GetPrabayarProduct as $value) {
                if ($value['product_id'] == $idProduk) {
                    $produkIni[] = $value;
                }
            }
            $harga = preg_replace('/[^0-9]/', '', $produkIni[0]['product_name']) + 2000;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = $request->cus_id;
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {


                // melanjutkan trx
                // membuat trxid_api
                $trxid_api = date('Ymdhis');

                // Post Prabayar
                $dataPostPrabayar = [
                    "destination" => $request->no_hp,
                    "product_id" => $request->produk,
                    "ref_id" => $trxid_api,
                ];
                $curlPostPrabayar = curl_init();
                curl_setopt_array($curlPostPrabayar, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $dataPostPrabayar,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responsePostPrabayar = curl_exec($curlPostPrabayar);
                $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
                // end Post Prabayar
                if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Terjadi Gangguan',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                } else { //success inquiry

                    $saldoNew = $dompetSekarang - $harga;
                    $Customer->saldo = $saldoNew;
                    $Customer->save();
                    // end pengurangan dompet

                    // Get status Trx 
                    $curlGetPrabayarHistory = curl_init();
                    curl_setopt_array($curlGetPrabayarHistory, array(
                        CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Authorization: ' . config('api.serpul_key_api'),
                        ),
                    ));
                    $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                    $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                    $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                    // end Get status Trx 

                    // mencatat ke mutasi
                    $mutasi = new Mutations;
                    $mutasi->username = $request->username;
                    $mutasi->jenis_transaksi = 'Token Listrik';
                    $mutasi->code = $request->produk;
                    $mutasi->phone = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $mutasi->note = "Pembelian Token Listrik | " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi diproses',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                }
            } elseif ($dompetSekarang < $harga) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak!',
                    'keterangan' => 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang),
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Ditolak!',
                'keterangan' => 'metode pembayaran tidak valid'
            ], 200);
        }
    }

    public function transaksiTB3(Request $request)
    {
        // return $request->all();

        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $hutangSaatIni = Hutang::where('customer_id', $request->cus_id)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak',
                    'keterangan' => 'Hutang Anda Sudah Banyak, Bayar Dulu !',
                ], 200);
            }
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination" => $request->no_hp,
                "product_id" => $request->produk,
                "ref_id" => $trxid_api,
            ];
            $curlPostPrabayar = curl_init();
            curl_setopt_array($curlPostPrabayar, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dataPostPrabayar,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Terjadi Gangguan',
                    'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                ], 200);
            } else { //success inquiry

                // Get status Trx 
                $curlGetPrabayarHistory = curl_init();
                curl_setopt_array($curlGetPrabayarHistory, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi->username = $request->username;
                $mutasi->jenis_transaksi = 'Transfer Bank';
                $mutasi->code = $request->produk;
                $mutasi->phone = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 7000;
                $mutasi->note = "Transfer Bank | " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 7000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Transfer Bank | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 7000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Transfer Bank | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = $request->cus_id;
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1.000',
                    ], 200);
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        } elseif ($request->metode_pembayaran == "Dompet") {
            $operatorProduk = $request->operatorProduk;
            $idProduk = $request->produk;
            // cekHargaProdukYangDibeli
            // Get Prabayar Product
            $curlGetPrabayarProduct = curl_init();
            curl_setopt_array($curlGetPrabayarProduct, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$operatorProduk",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
            $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
            $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
            // cekHargaProdukIni
            $produkIni = array();
            foreach ($GetPrabayarProduct as $value) {
                if ($value['product_id'] == $idProduk) {
                    $produkIni[] = $value;
                }
            }
            $harga = preg_replace('/[^0-9]/', '', $produkIni[0]['product_name']) + 7000;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = $request->cus_id;
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {


                // melanjutkan trx
                // membuat trxid_api
                $trxid_api = date('Ymdhis');

                // Post Prabayar
                $dataPostPrabayar = [
                    "destination" => $request->no_hp,
                    "product_id" => $request->produk,
                    "ref_id" => $trxid_api,
                ];
                $curlPostPrabayar = curl_init();
                curl_setopt_array($curlPostPrabayar, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $dataPostPrabayar,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responsePostPrabayar = curl_exec($curlPostPrabayar);
                $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
                // end Post Prabayar
                if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Terjadi Gangguan',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                } else { //success inquiry

                    $saldoNew = $dompetSekarang - $harga;
                    $Customer->saldo = $saldoNew;
                    $Customer->save();
                    // end pengurangan dompet

                    // Get status Trx 
                    $curlGetPrabayarHistory = curl_init();
                    curl_setopt_array($curlGetPrabayarHistory, array(
                        CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Authorization: ' . config('api.serpul_key_api'),
                        ),
                    ));
                    $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                    $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                    $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                    // end Get status Trx 

                    // mencatat ke mutasi
                    $mutasi = new Mutations;
                    $mutasi->username = $request->username;
                    $mutasi->jenis_transaksi = 'Transfer Bank';
                    $mutasi->code = $request->produk;
                    $mutasi->phone = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 7000;
                    $mutasi->note = "Transfer Bank | " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi diproses',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                }
            } elseif ($dompetSekarang < $harga) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak!',
                    'keterangan' => 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang),
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Ditolak!',
                'keterangan' => 'metode pembayaran tidak valid'
            ], 200);
        }
    }

    public function transaksiDANA3(Request $request)
    {
        // return $request->all();

        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $hutangSaatIni = Hutang::where('customer_id', $request->cus_id)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak',
                    'keterangan' => 'Hutang Anda Sudah Banyak, Bayar Dulu !',
                ], 200);
            }
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination" => $request->no_hp,
                "product_id" => $request->produk,
                "ref_id" => $trxid_api,
            ];
            $curlPostPrabayar = curl_init();
            curl_setopt_array($curlPostPrabayar, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dataPostPrabayar,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Terjadi Gangguan',
                    'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                ], 200);
            } else { //success inquiry

                // Get status Trx 
                $curlGetPrabayarHistory = curl_init();
                curl_setopt_array($curlGetPrabayarHistory, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi->username = $request->username;
                $mutasi->jenis_transaksi = 'DANA';
                $mutasi->code = $request->produk;
                $mutasi->phone = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                $mutasi->note = "DANA | " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "DANA | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "DANA | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = $request->cus_id;
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1.000',
                    ], 200);
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        } elseif ($request->metode_pembayaran == "Dompet") {
            $operatorProduk = $request->operatorProduk;
            $idProduk = $request->produk;
            // cekHargaProdukYangDibeli
            // Get Prabayar Product
            $curlGetPrabayarProduct = curl_init();
            curl_setopt_array($curlGetPrabayarProduct, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$operatorProduk",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
            $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
            $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
            // cekHargaProdukIni
            $produkIni = array();
            foreach ($GetPrabayarProduct as $value) {
                if ($value['product_id'] == $idProduk) {
                    $produkIni[] = $value;
                }
            }
            $harga = preg_replace('/[^0-9]/', '', $produkIni[0]['product_name']) + 2000;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = $request->cus_id;
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {


                // melanjutkan trx
                // membuat trxid_api
                $trxid_api = date('Ymdhis');

                // Post Prabayar
                $dataPostPrabayar = [
                    "destination" => $request->no_hp,
                    "product_id" => $request->produk,
                    "ref_id" => $trxid_api,
                ];
                $curlPostPrabayar = curl_init();
                curl_setopt_array($curlPostPrabayar, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $dataPostPrabayar,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responsePostPrabayar = curl_exec($curlPostPrabayar);
                $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
                // end Post Prabayar
                if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Terjadi Gangguan',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                } else { //success inquiry

                    $saldoNew = $dompetSekarang - $harga;
                    $Customer->saldo = $saldoNew;
                    $Customer->save();
                    // end pengurangan dompet

                    // Get status Trx 
                    $curlGetPrabayarHistory = curl_init();
                    curl_setopt_array($curlGetPrabayarHistory, array(
                        CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Authorization: ' . config('api.serpul_key_api'),
                        ),
                    ));
                    $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                    $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                    $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                    // end Get status Trx 

                    // mencatat ke mutasi
                    $mutasi = new Mutations;
                    $mutasi->username = $request->username;
                    $mutasi->jenis_transaksi = 'DANA';
                    $mutasi->code = $request->produk;
                    $mutasi->phone = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $mutasi->note = "DANA | " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi diproses',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                }
            } elseif ($dompetSekarang < $harga) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak!',
                    'keterangan' => 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang),
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Ditolak!',
                'keterangan' => 'metode pembayaran tidak valid'
            ], 200);
        }
    }

    public function transaksiOVO3(Request $request)
    {
        // return $request->all();

        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $hutangSaatIni = Hutang::where('customer_id', $request->cus_id)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak',
                    'keterangan' => 'Hutang Anda Sudah Banyak, Bayar Dulu !',
                ], 200);
            }
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination" => $request->no_hp,
                "product_id" => $request->produk,
                "ref_id" => $trxid_api,
            ];
            $curlPostPrabayar = curl_init();
            curl_setopt_array($curlPostPrabayar, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dataPostPrabayar,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Terjadi Gangguan',
                    'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                ], 200);
            } else { //success inquiry

                // Get status Trx 
                $curlGetPrabayarHistory = curl_init();
                curl_setopt_array($curlGetPrabayarHistory, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi->username = $request->username;
                $mutasi->jenis_transaksi = 'OVO';
                $mutasi->code = $request->produk;
                $mutasi->phone = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                $mutasi->note = "OVO | " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "OVO | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "OVO | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = $request->cus_id;
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1.000',
                    ], 200);
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        } elseif ($request->metode_pembayaran == "Dompet") {
            $operatorProduk = $request->operatorProduk;
            $idProduk = $request->produk;
            // cekHargaProdukYangDibeli
            // Get Prabayar Product
            $curlGetPrabayarProduct = curl_init();
            curl_setopt_array($curlGetPrabayarProduct, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$operatorProduk",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
            $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
            $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
            // cekHargaProdukIni
            $produkIni = array();
            foreach ($GetPrabayarProduct as $value) {
                if ($value['product_id'] == $idProduk) {
                    $produkIni[] = $value;
                }
            }
            $harga = preg_replace('/[^0-9]/', '', $produkIni[0]['product_name']) + 2000;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = $request->cus_id;
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {


                // melanjutkan trx
                // membuat trxid_api
                $trxid_api = date('Ymdhis');

                // Post Prabayar
                $dataPostPrabayar = [
                    "destination" => $request->no_hp,
                    "product_id" => $request->produk,
                    "ref_id" => $trxid_api,
                ];
                $curlPostPrabayar = curl_init();
                curl_setopt_array($curlPostPrabayar, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $dataPostPrabayar,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responsePostPrabayar = curl_exec($curlPostPrabayar);
                $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
                // end Post Prabayar
                if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Terjadi Gangguan',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                } else { //success inquiry

                    $saldoNew = $dompetSekarang - $harga;
                    $Customer->saldo = $saldoNew;
                    $Customer->save();
                    // end pengurangan dompet

                    // Get status Trx 
                    $curlGetPrabayarHistory = curl_init();
                    curl_setopt_array($curlGetPrabayarHistory, array(
                        CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Authorization: ' . config('api.serpul_key_api'),
                        ),
                    ));
                    $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                    $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                    $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                    // end Get status Trx 

                    // mencatat ke mutasi
                    $mutasi = new Mutations;
                    $mutasi->username = $request->username;
                    $mutasi->jenis_transaksi = 'OVO';
                    $mutasi->code = $request->produk;
                    $mutasi->phone = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $mutasi->note = "OVO | " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi diproses',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                }
            } elseif ($dompetSekarang < $harga) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak!',
                    'keterangan' => 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang),
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Ditolak!',
                'keterangan' => 'metode pembayaran tidak valid'
            ], 200);
        }
    }

    public function transaksiShopeePay3(Request $request)
    {
        // return $request->all();

        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $hutangSaatIni = Hutang::where('customer_id', $request->cus_id)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak',
                    'keterangan' => 'Hutang Anda Sudah Banyak, Bayar Dulu !',
                ], 200);
            }
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination" => $request->no_hp,
                "product_id" => $request->produk,
                "ref_id" => $trxid_api,
            ];
            $curlPostPrabayar = curl_init();
            curl_setopt_array($curlPostPrabayar, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dataPostPrabayar,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Terjadi Gangguan',
                    'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                ], 200);
            } else { //success inquiry

                // Get status Trx 
                $curlGetPrabayarHistory = curl_init();
                curl_setopt_array($curlGetPrabayarHistory, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi->username = $request->username;
                $mutasi->jenis_transaksi = 'ShopeePay';
                $mutasi->code = $request->produk;
                $mutasi->phone = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 1000;
                $mutasi->note = "ShopeePay | " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 1000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "ShopeePay | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 1000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "ShopeePay | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = $request->cus_id;
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1.000',
                    ], 200);
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        } elseif ($request->metode_pembayaran == "Dompet") {
            $operatorProduk = $request->operatorProduk;
            $idProduk = $request->produk;
            // cekHargaProdukYangDibeli
            // Get Prabayar Product
            $curlGetPrabayarProduct = curl_init();
            curl_setopt_array($curlGetPrabayarProduct, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$operatorProduk",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
            $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
            $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
            // cekHargaProdukIni
            $produkIni = array();
            foreach ($GetPrabayarProduct as $value) {
                if ($value['product_id'] == $idProduk) {
                    $produkIni[] = $value;
                }
            }
            $harga = preg_replace('/[^0-9]/', '', $produkIni[0]['product_name']) + 1000;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = $request->cus_id;
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {


                // melanjutkan trx
                // membuat trxid_api
                $trxid_api = date('Ymdhis');

                // Post Prabayar
                $dataPostPrabayar = [
                    "destination" => $request->no_hp,
                    "product_id" => $request->produk,
                    "ref_id" => $trxid_api,
                ];
                $curlPostPrabayar = curl_init();
                curl_setopt_array($curlPostPrabayar, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $dataPostPrabayar,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responsePostPrabayar = curl_exec($curlPostPrabayar);
                $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
                // end Post Prabayar
                if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Terjadi Gangguan',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                } else { //success inquiry

                    $saldoNew = $dompetSekarang - $harga;
                    $Customer->saldo = $saldoNew;
                    $Customer->save();
                    // end pengurangan dompet

                    // Get status Trx 
                    $curlGetPrabayarHistory = curl_init();
                    curl_setopt_array($curlGetPrabayarHistory, array(
                        CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Authorization: ' . config('api.serpul_key_api'),
                        ),
                    ));
                    $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                    $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                    $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                    // end Get status Trx 

                    // mencatat ke mutasi
                    $mutasi = new Mutations;
                    $mutasi->username = $request->username;
                    $mutasi->jenis_transaksi = 'ShopeePay';
                    $mutasi->code = $request->produk;
                    $mutasi->phone = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 1000;
                    $mutasi->note = "ShopeePay | " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi diproses',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                }
            } elseif ($dompetSekarang < $harga) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak!',
                    'keterangan' => 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang),
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Ditolak!',
                'keterangan' => 'metode pembayaran tidak valid'
            ], 200);
        }
    }

    public function transaksiGoPay3(Request $request)
    {
        // return $request->all();

        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $hutangSaatIni = Hutang::where('customer_id', $request->cus_id)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak',
                    'keterangan' => 'Hutang Anda Sudah Banyak, Bayar Dulu !',
                ], 200);
            }
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination" => $request->no_hp,
                "product_id" => $request->produk,
                "ref_id" => $trxid_api,
            ];
            $curlPostPrabayar = curl_init();
            curl_setopt_array($curlPostPrabayar, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dataPostPrabayar,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Terjadi Gangguan',
                    'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                ], 200);
            } else { //success inquiry

                // Get status Trx 
                $curlGetPrabayarHistory = curl_init();
                curl_setopt_array($curlGetPrabayarHistory, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi->username = $request->username;
                $mutasi->jenis_transaksi = 'GoPay';
                $mutasi->code = $request->produk;
                $mutasi->phone = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                $mutasi->note = "GoPay | " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "GoPay | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "GoPay | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = $request->cus_id;
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1.000',
                    ], 200);
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        } elseif ($request->metode_pembayaran == "Dompet") {
            $operatorProduk = $request->operatorProduk;
            $idProduk = $request->produk;
            // cekHargaProdukYangDibeli
            // Get Prabayar Product
            $curlGetPrabayarProduct = curl_init();
            curl_setopt_array($curlGetPrabayarProduct, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$operatorProduk",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
            $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
            $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
            // cekHargaProdukIni
            $produkIni = array();
            foreach ($GetPrabayarProduct as $value) {
                if ($value['product_id'] == $idProduk) {
                    $produkIni[] = $value;
                }
            }
            $harga = preg_replace('/[^0-9]/', '', $produkIni[0]['product_name']) + 2000;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = $request->cus_id;
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {


                // melanjutkan trx
                // membuat trxid_api
                $trxid_api = date('Ymdhis');

                // Post Prabayar
                $dataPostPrabayar = [
                    "destination" => $request->no_hp,
                    "product_id" => $request->produk,
                    "ref_id" => $trxid_api,
                ];
                $curlPostPrabayar = curl_init();
                curl_setopt_array($curlPostPrabayar, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $dataPostPrabayar,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responsePostPrabayar = curl_exec($curlPostPrabayar);
                $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
                // end Post Prabayar
                if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Terjadi Gangguan',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                } else { //success inquiry

                    $saldoNew = $dompetSekarang - $harga;
                    $Customer->saldo = $saldoNew;
                    $Customer->save();
                    // end pengurangan dompet

                    // Get status Trx 
                    $curlGetPrabayarHistory = curl_init();
                    curl_setopt_array($curlGetPrabayarHistory, array(
                        CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Authorization: ' . config('api.serpul_key_api'),
                        ),
                    ));
                    $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                    $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                    $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                    // end Get status Trx 

                    // mencatat ke mutasi
                    $mutasi = new Mutations;
                    $mutasi->username = $request->username;
                    $mutasi->jenis_transaksi = 'GoPay';
                    $mutasi->code = $request->produk;
                    $mutasi->phone = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $mutasi->note = "GoPay | " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi diproses',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                }
            } elseif ($dompetSekarang < $harga) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak!',
                    'keterangan' => 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang),
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Ditolak!',
                'keterangan' => 'metode pembayaran tidak valid'
            ], 200);
        }
    }

    public function transaksiLinkAja3(Request $request)
    {
        // return $request->all();

        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $hutangSaatIni = Hutang::where('customer_id', $request->cus_id)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak',
                    'keterangan' => 'Hutang Anda Sudah Banyak, Bayar Dulu !',
                ], 200);
            }
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination" => $request->no_hp,
                "product_id" => $request->produk,
                "ref_id" => $trxid_api,
            ];
            $curlPostPrabayar = curl_init();
            curl_setopt_array($curlPostPrabayar, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dataPostPrabayar,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Terjadi Gangguan',
                    'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                ], 200);
            } else { //success inquiry

                // Get status Trx 
                $curlGetPrabayarHistory = curl_init();
                curl_setopt_array($curlGetPrabayarHistory, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi->username = $request->username;
                $mutasi->jenis_transaksi = 'LinkAja';
                $mutasi->code = $request->produk;
                $mutasi->phone = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                $mutasi->note = "LinkAja | " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "LinkAja | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "LinkAja | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = $request->cus_id;
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1.000',
                    ], 200);
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        } elseif ($request->metode_pembayaran == "Dompet") {
            $operatorProduk = $request->operatorProduk;
            $idProduk = $request->produk;
            // cekHargaProdukYangDibeli
            // Get Prabayar Product
            $curlGetPrabayarProduct = curl_init();
            curl_setopt_array($curlGetPrabayarProduct, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$operatorProduk",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
            $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
            $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
            // cekHargaProdukIni
            $produkIni = array();
            foreach ($GetPrabayarProduct as $value) {
                if ($value['product_id'] == $idProduk) {
                    $produkIni[] = $value;
                }
            }
            $harga = preg_replace('/[^0-9]/', '', $produkIni[0]['product_name']) + 2000;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = $request->cus_id;
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {


                // melanjutkan trx
                // membuat trxid_api
                $trxid_api = date('Ymdhis');

                // Post Prabayar
                $dataPostPrabayar = [
                    "destination" => $request->no_hp,
                    "product_id" => $request->produk,
                    "ref_id" => $trxid_api,
                ];
                $curlPostPrabayar = curl_init();
                curl_setopt_array($curlPostPrabayar, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $dataPostPrabayar,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responsePostPrabayar = curl_exec($curlPostPrabayar);
                $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
                // end Post Prabayar
                if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Terjadi Gangguan',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                } else { //success inquiry

                    $saldoNew = $dompetSekarang - $harga;
                    $Customer->saldo = $saldoNew;
                    $Customer->save();
                    // end pengurangan dompet

                    // Get status Trx 
                    $curlGetPrabayarHistory = curl_init();
                    curl_setopt_array($curlGetPrabayarHistory, array(
                        CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Authorization: ' . config('api.serpul_key_api'),
                        ),
                    ));
                    $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                    $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                    $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                    // end Get status Trx 

                    // mencatat ke mutasi
                    $mutasi = new Mutations;
                    $mutasi->username = $request->username;
                    $mutasi->jenis_transaksi = 'LinkAja';
                    $mutasi->code = $request->produk;
                    $mutasi->phone = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $mutasi->note = "LinkAja | " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi diproses',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                }
            } elseif ($dompetSekarang < $harga) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak!',
                    'keterangan' => 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang),
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Ditolak!',
                'keterangan' => 'metode pembayaran tidak valid'
            ], 200);
        }
    }

    public function transaksiWifiId3(Request $request)
    {
        // return $request->all();

        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $hutangSaatIni = Hutang::where('customer_id', $request->cus_id)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak',
                    'keterangan' => 'Hutang Anda Sudah Banyak, Bayar Dulu !',
                ], 200);
            }
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination" => $request->no_hp,
                "product_id" => $request->produk,
                "ref_id" => $trxid_api,
            ];
            $curlPostPrabayar = curl_init();
            curl_setopt_array($curlPostPrabayar, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dataPostPrabayar,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Terjadi Gangguan',
                    'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                ], 200);
            } else { //success inquiry

                // Get status Trx 
                $curlGetPrabayarHistory = curl_init();
                curl_setopt_array($curlGetPrabayarHistory, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi->username = $request->username;
                $mutasi->jenis_transaksi = 'Wifi ID';
                $mutasi->code = $request->produk;
                $mutasi->phone = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                $mutasi->note = "Wifi ID | " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Wifi ID | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Wifi ID | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = $request->cus_id;
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1.000',
                    ], 200);
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        } elseif ($request->metode_pembayaran == "Dompet") {
            $operatorProduk = $request->operatorProduk;
            $idProduk = $request->produk;
            // cekHargaProdukYangDibeli
            // Get Prabayar Product
            $curlGetPrabayarProduct = curl_init();
            curl_setopt_array($curlGetPrabayarProduct, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$operatorProduk",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
            $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
            $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
            // cekHargaProdukIni
            $produkIni = array();
            foreach ($GetPrabayarProduct as $value) {
                if ($value['product_id'] == $idProduk) {
                    $produkIni[] = $value;
                }
            }
            $harga = $produkIni[0]['product_price'] + 2000;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = $request->cus_id;
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {


                // melanjutkan trx
                // membuat trxid_api
                $trxid_api = date('Ymdhis');

                // Post Prabayar
                $dataPostPrabayar = [
                    "destination" => $request->no_hp,
                    "product_id" => $request->produk,
                    "ref_id" => $trxid_api,
                ];
                $curlPostPrabayar = curl_init();
                curl_setopt_array($curlPostPrabayar, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $dataPostPrabayar,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responsePostPrabayar = curl_exec($curlPostPrabayar);
                $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
                // end Post Prabayar
                if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Terjadi Gangguan',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                } else { //success inquiry

                    $saldoNew = $dompetSekarang - $harga;
                    $Customer->saldo = $saldoNew;
                    $Customer->save();
                    // end pengurangan dompet

                    // Get status Trx 
                    $curlGetPrabayarHistory = curl_init();
                    curl_setopt_array($curlGetPrabayarHistory, array(
                        CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Authorization: ' . config('api.serpul_key_api'),
                        ),
                    ));
                    $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                    $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                    $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                    // end Get status Trx 

                    // mencatat ke mutasi
                    $mutasi = new Mutations;
                    $mutasi->username = $request->username;
                    $mutasi->jenis_transaksi = 'Wifi ID';
                    $mutasi->code = $request->produk;
                    $mutasi->phone = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = $GetPrabayarHistory['price'];
                    +2000;
                    $mutasi->note = "Wifi ID | " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi diproses',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                }
            } elseif ($dompetSekarang < $harga) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak!',
                    'keterangan' => 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang),
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Ditolak!',
                'keterangan' => 'metode pembayaran tidak valid'
            ], 200);
        }
    }

    public function transaksivgml3(Request $request)
    {
        // return $request->all();

        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $hutangSaatIni = Hutang::where('customer_id', $request->cus_id)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak',
                    'keterangan' => 'Hutang Anda Sudah Banyak, Bayar Dulu !',
                ], 200);
            }
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination" => $request->no_hp,
                "product_id" => $request->produk,
                "ref_id" => $trxid_api,
            ];
            $curlPostPrabayar = curl_init();
            curl_setopt_array($curlPostPrabayar, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dataPostPrabayar,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Terjadi Gangguan',
                    'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                ], 200);
            } else { //success inquiry

                // Get status Trx 
                $curlGetPrabayarHistory = curl_init();
                curl_setopt_array($curlGetPrabayarHistory, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi->username = $request->username;
                $mutasi->jenis_transaksi = 'Diamond Mobile Legend';
                $mutasi->code = $request->produk;
                $mutasi->phone = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                $mutasi->note = "Diamond Mobile Legend | " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Diamond Mobile Legend | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Diamond Mobile Legend | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = $request->cus_id;
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1.000',
                    ], 200);
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        } elseif ($request->metode_pembayaran == "Dompet") {
            $operatorProduk = $request->operatorProduk;
            $idProduk = $request->produk;
            // cekHargaProdukYangDibeli
            // Get Prabayar Product
            $curlGetPrabayarProduct = curl_init();
            curl_setopt_array($curlGetPrabayarProduct, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$operatorProduk",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
            $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
            $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
            // cekHargaProdukIni
            $produkIni = array();
            foreach ($GetPrabayarProduct as $value) {
                if ($value['product_id'] == $idProduk) {
                    $produkIni[] = $value;
                }
            }
            $harga = $produkIni[0]['product_price'] + 2000;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = $request->cus_id;
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {


                // melanjutkan trx
                // membuat trxid_api
                $trxid_api = date('Ymdhis');

                // Post Prabayar
                $dataPostPrabayar = [
                    "destination" => $request->no_hp,
                    "product_id" => $request->produk,
                    "ref_id" => $trxid_api,
                ];
                $curlPostPrabayar = curl_init();
                curl_setopt_array($curlPostPrabayar, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $dataPostPrabayar,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responsePostPrabayar = curl_exec($curlPostPrabayar);
                $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
                // end Post Prabayar
                if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Terjadi Gangguan',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                } else { //success inquiry

                    $saldoNew = $dompetSekarang - $harga;
                    $Customer->saldo = $saldoNew;
                    $Customer->save();
                    // end pengurangan dompet

                    // Get status Trx 
                    $curlGetPrabayarHistory = curl_init();
                    curl_setopt_array($curlGetPrabayarHistory, array(
                        CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Authorization: ' . config('api.serpul_key_api'),
                        ),
                    ));
                    $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                    $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                    $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                    // end Get status Trx 

                    // mencatat ke mutasi
                    $mutasi = new Mutations;
                    $mutasi->username = $request->username;
                    $mutasi->jenis_transaksi = 'Diamond Mobile Legend';
                    $mutasi->code = $request->produk;
                    $mutasi->phone = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = $GetPrabayarHistory['price'];
                    +2000;
                    $mutasi->note = "Diamond Mobile Legend | " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi diproses',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                }
            } elseif ($dompetSekarang < $harga) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak!',
                    'keterangan' => 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang),
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Ditolak!',
                'keterangan' => 'metode pembayaran tidak valid'
            ], 200);
        }
    }

    public function transaksivgff3(Request $request)
    {
        // return $request->all();

        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $hutangSaatIni = Hutang::where('customer_id', $request->cus_id)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak',
                    'keterangan' => 'Hutang Anda Sudah Banyak, Bayar Dulu !',
                ], 200);
            }
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination" => $request->no_hp,
                "product_id" => $request->produk,
                "ref_id" => $trxid_api,
            ];
            $curlPostPrabayar = curl_init();
            curl_setopt_array($curlPostPrabayar, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dataPostPrabayar,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Terjadi Gangguan',
                    'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                ], 200);
            } else { //success inquiry

                // Get status Trx 
                $curlGetPrabayarHistory = curl_init();
                curl_setopt_array($curlGetPrabayarHistory, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi->username = $request->username;
                $mutasi->jenis_transaksi = 'Diamond Free Fire';
                $mutasi->code = $request->produk;
                $mutasi->phone = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                $mutasi->note = "Diamond Free Fire | " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Diamond Free Fire | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Diamond Free Fire | " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = $request->cus_id;
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1.000',
                    ], 200);
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        } elseif ($request->metode_pembayaran == "Dompet") {
            $operatorProduk = $request->operatorProduk;
            $idProduk = $request->produk;
            // cekHargaProdukYangDibeli
            // Get Prabayar Product
            $curlGetPrabayarProduct = curl_init();
            curl_setopt_array($curlGetPrabayarProduct, array(
                CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$operatorProduk",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
            $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct, true);
            $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
            // cekHargaProdukIni
            $produkIni = array();
            foreach ($GetPrabayarProduct as $value) {
                if ($value['product_id'] == $idProduk) {
                    $produkIni[] = $value;
                }
            }
            $harga = $produkIni[0]['product_price'] + 2000;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = $request->cus_id;
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {


                // melanjutkan trx
                // membuat trxid_api
                $trxid_api = date('Ymdhis');

                // Post Prabayar
                $dataPostPrabayar = [
                    "destination" => $request->no_hp,
                    "product_id" => $request->produk,
                    "ref_id" => $trxid_api,
                ];
                $curlPostPrabayar = curl_init();
                curl_setopt_array($curlPostPrabayar, array(
                    CURLOPT_URL => "https://api.serpul.co.id/prabayar/order",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $dataPostPrabayar,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responsePostPrabayar = curl_exec($curlPostPrabayar);
                $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
                // end Post Prabayar
                if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Terjadi Gangguan',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                } else { //success inquiry

                    $saldoNew = $dompetSekarang - $harga;
                    $Customer->saldo = $saldoNew;
                    $Customer->save();
                    // end pengurangan dompet

                    // Get status Trx 
                    $curlGetPrabayarHistory = curl_init();
                    curl_setopt_array($curlGetPrabayarHistory, array(
                        CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/$trxid_api",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Authorization: ' . config('api.serpul_key_api'),
                        ),
                    ));
                    $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                    $decodeResponseGetPrabayarHistory = json_decode($responseGetPrabayarHistory, 1);
                    $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                    // end Get status Trx 

                    // mencatat ke mutasi
                    $mutasi = new Mutations;
                    $mutasi->username = $request->username;
                    $mutasi->jenis_transaksi = 'Diamond Free Fire';
                    $mutasi->code = $request->produk;
                    $mutasi->phone = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = $GetPrabayarHistory['price'];
                    +2000;
                    $mutasi->note = "Diamond Free Fire | " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi diproses',
                        'keterangan' => $decodeResponsePostPrabayar['responseMessage'],
                    ], 200);
                }
            } elseif ($dompetSekarang < $harga) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak!',
                    'keterangan' => 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang),
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Ditolak!',
                'keterangan' => 'metode pembayaran tidak valid'
            ], 200);
        }
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

        return response()->json([
            'status' => 'success',
            'message' => 'Buat Kode Berhasil',
            'message' => 'Kode Akses Berhasil dibuat, silahkan simpan dengan baik',
            'kode' => $random,
        ], 200);
    }

    public function CheckTagihanPLN($id)
    {
        $trxid_api = date('Ymdhis');

        // Post Prabayar
        $dataPostPrabayar = [
            "no_pelanggan" => $id,
            "product_id" => 'PLN',
            "ref_id" => $trxid_api,
        ];
        $curlPostPrabayar = curl_init();
        curl_setopt_array($curlPostPrabayar, array(
            CURLOPT_URL => "https://api.serpul.co.id/pascabayar/check",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $dataPostPrabayar,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Authorization: ' . config('api.serpul_key_api'),
            ),
        ));
        $responsePostPrabayar = curl_exec($curlPostPrabayar);
        return $decodeResponsePostPrabayar = json_decode($responsePostPrabayar, true);
        // end Post Prabayar
    }

    public function transaksiTLPasca(Request $request)
    {
        // return $request->all();

        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $hutangSaatIni = Hutang::where('customer_id', $request->cus_id)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak',
                    'keterangan' => 'Hutang Anda Sudah Banyak, Bayar Dulu !',
                ], 200);
            }
            // membuat trxid_api
            $trxid_api = $request->ref_id;

            // Post Prabayar
            $dataPostPrabayar = [
                "ref_id" => $trxid_api,
            ];
            $curlPostPrabayar = curl_init();
            curl_setopt_array($curlPostPrabayar, array(
                CURLOPT_URL => "https://api.serpul.co.id/pascabayar/pay",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $dataPostPrabayar,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: ' . config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar = json_decode($responsePostPrabayar);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar->responseCode == 400) { //failed inquiry
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Terjadi Gangguan',
                    'keterangan' => $decodeResponsePostPrabayar->responseMessage,
                ], 200);
            } else { //success inquiry

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi->username = $request->username;
                $mutasi->jenis_transaksi = 'Tagihan Listrik';
                $mutasi->phone = $request->no_pelanggan;
                $mutasi->status = $decodeResponsePostPrabayar->responseData->status;
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $request->total_bayar;
                $mutasi->harga_jual = $request->total_tagihan;
                $mutasi->note = "Pembayaran Tagihan Listrik | " . $request->nama_pelanggan;
                $mutasi->save();

                if ($decodeResponsePostPrabayar->responseData->status == "PENDING" or $decodeResponsePostPrabayar->responseData->status == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $request->total_tagihan;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembayaran Tagihan Listrik | " . $request->nama_pelanggan;
                    $hutang->save();
                } elseif ($decodeResponsePostPrabayar->responseData->status == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = $request->cus_id;
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $request->total_tagihan;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembayaran Tagihan Listrik | " . $request->nama_pelanggan;
                    $hutang->save();

                    // menambah point
                    $idCustomer = $request->cus_id;
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil',
                        'keterangan' => 'Coin Bertambah 1.000',
                    ], 200);
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $cus = Customers::where('username', $request->username)->first();
                $getUsername = $cus->username;
                $getNama = $cus->name;
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
            }
        } elseif ($request->metode_pembayaran == "Dompet") {

            $harga = $request->total_tagihan;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = $request->cus_id;
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {


                // melanjutkan trx
                // membuat trxid_api
                $trxid_api = $request->ref_id;

                // Post Prabayar
                $dataPostPrabayar = [
                    "ref_id" => $trxid_api,
                ];
                $curlPostPrabayar = curl_init();
                curl_setopt_array($curlPostPrabayar, array(
                    CURLOPT_URL => "https://api.serpul.co.id/pascabayar/pay",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $dataPostPrabayar,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Authorization: ' . config('api.serpul_key_api'),
                    ),
                ));
                $responsePostPrabayar = curl_exec($curlPostPrabayar);
                $decodeResponsePostPrabayar = json_decode($responsePostPrabayar);
                // end Post Prabayar
                if ($decodeResponsePostPrabayar->responseCode == 400) { //failed inquiry
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Terjadi Gangguan',
                        'keterangan' => $decodeResponsePostPrabayar->responseMessage,
                    ], 200);
                } else { //success inquiry

                    $saldoNew = $dompetSekarang - $harga;
                    $Customer->saldo = $saldoNew;
                    $Customer->save();
                    // end pengurangan dompet

                    // mencatat ke mutasi
                    $mutasi = new Mutations;
                    $mutasi->username = $request->username;
                    $mutasi->jenis_transaksi = 'Tagihan Listrik';
                    $mutasi->phone = $request->no_pelanggan;
                    $mutasi->status = $decodeResponsePostPrabayar->responseData->status;
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $request->total_bayar;
                    $mutasi->harga_jual = $request->total_tagihan;
                    $mutasi->note = "Pembayaran Tagihan Listrik | " . $request->nama_pelanggan;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $cus = Customers::where('username', $request->username)->first();
                    $getUsername = $cus->username;
                    $getNama = $cus->name;
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi diproses',
                        'keterangan' => $decodeResponsePostPrabayar->responseMessage,
                    ], 200);
                }
            } elseif ($dompetSekarang < $harga) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaksi Ditolak!',
                    'keterangan' => 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang),
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaksi Ditolak!',
                'keterangan' => 'metode pembayaran tidak valid'
            ], 200);
        }
    }
}
