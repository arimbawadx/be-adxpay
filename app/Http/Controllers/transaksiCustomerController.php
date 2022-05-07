<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mutations;
use App\Models\Customers;
use App\Models\Hutang;
use Alert;

class transaksiCustomerController extends Controller
{
    public function UpdatePendingTrx($id)
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
            return back()->withInput();
        }

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
                    Alert::error('Transaksi Gagal', 'Terjadi gangguan, hubungi admin');
                } else {
                    $historyHarga = $mutasi->harga_jual;
                    $username = session()->get('dataLoginCustomers')['username'];
                    // update saldo customer
                    $UpdateSaldoCustomer = Customers::where('username', $username)->first();
                    $UpdateSaldoCustomer->saldo = $UpdateSaldoCustomer->saldo + $historyHarga;
                    $UpdateSaldoCustomer->save();
                }
            } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                // menambah point
                $idCustomer = session()->get('dataLoginCustomers')['id'];
                $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                $UpdatePointCustomer->save();
                Alert::success('Transaksi Berhasil', 'Coin Bertambah 1.000');
            }
        }
        return back()->withInput();
    }

    public function transaksiBukti($trxid_api)
    {
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
        // return $GetPrabayarHistory;
        // end Get status Trx 

        if ($GetPrabayarHistory == null) {
            // perubahan mutasi
            $mutasi = Mutations::where('trxid_api', $trxid_api)->get()->first();
            $mutasi->status = "KADALUARSA";
            $mutasi->save();
            Alert::error('Transaksi Lama', 'Bukti transaksi telah dihapus');
            return back()->withInput();
        }

        return view('customers.pages.transaksiBukti', compact('GetPrabayarHistory', 'trxid_api'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function transaksiPIU1()
    {
        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'Pulsa Isi Ulang')->where('username', $username)->get()->sortByDesc('created_at');
        return view('customers.pages.transaksiPIU1', compact('mutasi'));
    }

    public function transaksiPIU2(Request $request)
    {
        // cek provider
        $phone = $request->no_hp;
        $phone4 = substr(trim($phone), 0, 4); //memotong no hp 4 awal
        if (in_array($phone4, explode(',', '0852,0853,0811,0812,0813,0821,0822,0823,0851'))) {
            $idProduk = 'PIUTSL';
        } elseif (in_array($phone4, explode(',', '0831,0832,0833,0838'))) {
            $idProduk = 'PIUAX';
        } elseif (in_array($phone4, explode(',', '0859,0877,0878,0817,0818,0819'))) {
            $idProduk = 'PIUXL';
        } elseif (in_array($phone4, explode(',', '0814,0815,0816,0855,0856,0857,0858,0813'))) {
            $idProduk = 'PIUIST';
        } elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
            $idProduk = 'PIUTR';
        } elseif (in_array($phone4, explode(',', '0881,0882,0883,0884,0885,0886,0887,0888,0889'))) {
            $idProduk = 'PIUSF';
        } elseif (in_array($phone4, explode(',', '0851,08515'))) {
            $idProduk = 'PIUBY';
        } elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879,0831,0838,08591,0817,0818,0819,0859,0877,0878,0879,0859,0832'))) {
            $idProduk = 'PIUAXL';
        } elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879,0831,0838,08591,0817,0818,0819,0859,0877,0878,0879,0859,0832'))) {
            $idProduk = 'PIUAXLP';
        } elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879,0831,0838,08591,0817,0818,0819,0859,0877,0878,0879,0859,0832'))) {
            $idProduk = 'PIUAXLSP';
        } elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
            $idProduk = 'PIUTRP';
        } elseif (in_array($phone4, explode(',', '0814,0815,0816,0855,0856,0857,0858'))) {
            $idProduk = 'PIUISTP';
        } elseif (in_array($phone4, explode(',', '0814,0815,0816,0855,0856,0857,0858,0813'))) {
            $idProduk = 'PIUISTG';
        }
        // end cek provider


        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$idProduk",
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
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product

        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'Pulsa Isi Ulang')->where('username', $username)->get()->sortByDesc('created_at');
        $saldo = Customers::where('username', $username)->get()->first();
        return view('customers.pages.transaksiPIU2', compact('mutasi', 'phone', 'GetPrabayarProduct', 'saldo', 'idProduk'));
    }

    public function transaksiPIU3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $CusID = session()->get('dataLoginCustomers')['id'];
            $hutangSaatIni = Hutang::where('customer_id', $CusID)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                Alert::error('Transaksi Ditolak', "Hutang Anda Sudah Banyak, Bayar Dulu !");
                return redirect('/customers/dashboard');
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
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cus/transaksi/PIU/1');
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
                $mutasi->username = session()->get('dataLoginCustomers')['username'];
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
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk Pulsa Isi Ulang " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk Pulsa Isi Ulang " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = session()->get('dataLoginCustomers')['id'];
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    Alert::success('Transaksi Berhasil', 'Coin Bertambah 1.000');
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = session()->get('dataLoginCustomers')['username'];
                $getNama = session()->get('dataLoginCustomers')['name'];
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx                
                return redirect('/cus/transaksi/PIU/1');
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
            $idCustomer = session()->get('dataLoginCustomers')['id'];
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {
                $saldoNew = $dompetSekarang - $harga;
                $Customer->saldo = $saldoNew;
                $Customer->save();
                // end pengurangan dompet

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
                    Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                    return redirect('/cus/transaksi/PIU/1');
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
                    $mutasi->username = session()->get('dataLoginCustomers')['username'];
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
                    $getUsername = session()->get('dataLoginCustomers')['username'];
                    $getNama = session()->get('dataLoginCustomers')['name'];
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return redirect('/cus/transaksi/PIU/1');
                }
            } elseif ($dompetSekarang < $harga) {
                Alert::error('Transaksi Ditolak!', 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang));
                return redirect('/cus/transaksi/PIU/1');
            }
        } else {
            return redirect('/cus/transaksi/PIU/1');
        }
    }


    public function transaksiPD1()
    {
        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'Paket Data Internet')->where('username', $username)->get()->sortByDesc('created_at');
        return view('customers.pages.transaksiPD1', compact('mutasi'));
    }

    public function transaksiPD2(Request $request)
    {
        // cek provider
        $phone = $request->no_hp;
        $phone4 = substr(trim($phone), 0, 4); //memotong no hp 4 awal
        // if (in_array($phone4, explode(',', '0852,0853,0811,0812,0813,0821,0822,0823,0851'))) {
        //     $idProduk = 'PDTSLHR';
        // }elseif (in_array($phone4, explode(',', '0852,0853,0811,0812,0813,0821,0822,0823'))) {
        //     $idProduk = 'PDTSLMG';}
        if (in_array($phone4, explode(',', '0852,0853,0811,0812,0813,0821,0822,0823,0851'))) {
            $idProduk = 'PDTSLBL';
            // }elseif (in_array($phone4, explode(',', '0852,0853,0811,0812,0813,0821,0822,0823'))) {
            //     $idProduk = 'PDTSLMM';
            // }elseif (in_array($phone4, explode(',', '0852,0853,0811,0812,0813,0821,0822,0823'))) {
            //     $idProduk = 'PDTSLGM';
            // }elseif (in_array($phone4, explode(',', '0852,0853,0811,0812,0813,0821,0822,0823'))) {
            //     $idProduk = 'PDTSLSS';
            // }elseif (in_array($phone4, explode(',', '0852,0853,0811,0812,0813,0821,0822,0823'))) {
            //     $idProduk = 'PDTSLBG';
            // }elseif (in_array($phone4, explode(',', '0852,0853,0823'))) {
            //     $idProduk = 'PDTSLAS';
            // }elseif (in_array($phone4, explode(',', '0822,0823'))) {
            //     $idProduk = 'PDTSLLP';
            // }elseif (in_array($phone4, explode(',', '0812,0813,0821,0823'))) {
            //     $idProduk = 'PDTSLSP';
            // }elseif (in_array($phone4, explode(',', '0852,0853,0811,0812,0813,0821,0822,0823'))) {
            //     $idProduk = 'PDTSLHU';
            // }elseif (in_array($phone4, explode(',', '0852,0853,0811,0812,0813,0821,0822,0823'))) {
            //     $idProduk = 'PDTSLVC';
            // }elseif (in_array($phone4, explode(',', '0831,0838'))) {
            //     $idProduk = 'PDAXHR';
            // }elseif (in_array($phone4, explode(',', '0831,0838'))) {
            //     $idProduk = 'PDAXMG';
        } elseif (in_array($phone4, explode(',', '0831,0832,0833,0838'))) {
            $idProduk = 'PDAXBL';
            // }elseif (in_array($phone4, explode(',', '0831,0838'))) {
            //     $idProduk = 'PDAXMM';
            // }elseif (in_array($phone4, explode(',', '0831,0838,0859'))) {
            //     $idProduk = 'PDAXVC';
            // }elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879'))) {
            //     $idProduk = 'PDXLHR';
            // }elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879'))) {
            //     $idProduk = 'PDXLMG';
        } elseif (in_array($phone4, explode(',', '0859,0877,0878,0817,0818,0819'))) {
            $idProduk = 'PDXLBL';
            // }elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879'))) {
            //     $idProduk = 'PDXLTH';
            // }elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879'))) {
            //     $idProduk = 'PDXLMM';
            // }elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879'))) {
            //     $idProduk = 'PDXLHU';
            // }elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879'))) {
            //     $idProduk = 'PDXLBJ';
            // }elseif (in_array($phone4, explode(',', '0814,0815,0816,0855,0856,0857,0858'))) {
            //     $idProduk = 'PDISTHR';
            // }elseif (in_array($phone4, explode(',', '0814,0815,0816,0855,0856,0857,0858'))) {
            //     $idProduk = 'PDISTMG';
        } elseif (in_array($phone4, explode(',', '0814,0815,0816,0855,0856,0857,0858'))) {
            $idProduk = 'PDISTBL';
            // }elseif (in_array($phone4, explode(',', '0814,0815,0816,0855,0856,0857,0858'))) {
            //     $idProduk = 'PDISTMM';
            // }elseif (in_array($phone4, explode(',', '0814,0815,0816,0855,0856,0857,0858'))) {
            //     $idProduk = 'PDISTLN';
            // }elseif (in_array($phone4, explode(',', '0814,0815,0816,0855,0856,0857,0858'))) {
            //     $idProduk = 'PDISTVC';
            // }elseif (in_array($phone4, explode(',', '0881,0882,0883,0884,0885,0886,0887,0888,0889'))) {
            //     $idProduk = 'PDSFHR';
            // }elseif (in_array($phone4, explode(',', '0881,0882,0883,0884,0885,0886,0887,0888,0889'))) {
            //     $idProduk = 'PDSFMG';
        } elseif (in_array($phone4, explode(',', '0881,0882,0883,0884,0885,0886,0887,0888,0889'))) {
            $idProduk = 'PDSFBL';
            // }elseif (in_array($phone4, explode(',', '0881,0882,0883,0884,0885,0886,0887,0888,0889'))) {
            //     $idProduk = 'PDSFMF';
            // }elseif (in_array($phone4, explode(',', '0881,0882,0883,0884,0885,0886,0887,0888,0889'))) {
            //     $idProduk = 'PDSFVC';
            // }elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
            //     $idProduk = 'PDTRHR';
            // }elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
            //     $idProduk = 'PDTRMG';
        } elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
            $idProduk = 'PDTRBL';
            // }elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
            //     $idProduk = 'PDTRMM';
            // }elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
            //     $idProduk = 'PDTRBG';
            // }elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
            //     $idProduk = 'PDTRKK';
            // }elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
            //     $idProduk = 'PDTRVC';
            // }elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
            //     $idProduk = 'PDTRD';
        } elseif (in_array($phone4, explode(',', '0852,0853,0811,0812,0813,0821,0822,0823,0851'))) {
            $idProduk = 'PDTSLUNLI';
        } elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
            $idProduk = 'PDTRB';
        }
        // end cek provider


        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$idProduk",
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
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'Paket Data Internet')->where('username', $username)->get()->sortByDesc('created_at');
        $saldo = Customers::where('username', $username)->get()->first();
        return view('customers.pages.transaksiPD2', compact('mutasi', 'phone', 'GetPrabayarProduct', 'saldo', 'idProduk'));
    }

    public function transaksiPD3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $CusID = session()->get('dataLoginCustomers')['id'];
            $hutangSaatIni = Hutang::where('customer_id', $CusID)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                Alert::error('Transaksi Ditolak', "Hutang Anda Sudah Banyak, Bayar Dulu !");
                return redirect('/customers/dashboard');
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
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cus/transaksi/PD/1');
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
                $mutasi->username = session()->get('dataLoginCustomers')['username'];
                $mutasi->jenis_transaksi = 'Paket Data Internet';
                $mutasi->code = $request->produk;
                $mutasi->phone = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = $GetPrabayarHistory['price'] + 5000;
                $mutasi->note = "Pembelian Paket Data Internet " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 5000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk Paket Data Internet " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 5000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk Paket Data Internet " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = session()->get('dataLoginCustomers')['id'];
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    Alert::success('Transaksi Berhasil', 'Coin Bertambah 1.000');
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = session()->get('dataLoginCustomers')['username'];
                $getNama = session()->get('dataLoginCustomers')['name'];
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
                return redirect('/cus/transaksi/PD/1');
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
            $idCustomer = session()->get('dataLoginCustomers')['id'];
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {
                $saldoNew = $dompetSekarang - $harga;
                $Customer->saldo = $saldoNew;
                $Customer->save();
                // end pengurangan dompet

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
                    Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                    return redirect('/cus/transaksi/PD/1');
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
                    $mutasi->username = session()->get('dataLoginCustomers')['username'];
                    $mutasi->jenis_transaksi = 'Paket Data Internet';
                    $mutasi->code = $request->produk;
                    $mutasi->phone = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = $GetPrabayarHistory['price'] + 5000;
                    $mutasi->note = "Pembelian Paket Data Internet " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();


                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $getUsername = session()->get('dataLoginCustomers')['username'];
                    $getNama = session()->get('dataLoginCustomers')['name'];
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx

                    return redirect('/cus/transaksi/PD/1');
                }
            } elseif ($dompetSekarang < $harga) {
                Alert::error('Transaksi Ditolak!', 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang));
                return redirect('/cus/transaksi/PD/1');
            }
        } else {
            return redirect('/cus/transaksi/PD/1');
        }
    }


    public function transaksiTL1()
    {
        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'Token Listrik')->where('username', $username)->get()->sortByDesc('created_at');
        return view('customers.pages.transaksiTL1', compact('mutasi'));
    }

    public function transaksiTL2(Request $request)
    {
        // cek provider
        $phone = $request->no_hp;
        $idProduk = "TLOP";
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$idProduk",
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
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'Token Listrik')->where('username', $username)->get()->sortByDesc('created_at');
        $saldo = Customers::where('username', $username)->get()->first();
        return view('customers.pages.transaksiTL2', compact('mutasi', 'phone', 'GetPrabayarProduct', 'saldo', 'idProduk'));
    }

    public function transaksiTL3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $CusID = session()->get('dataLoginCustomers')['id'];
            $hutangSaatIni = Hutang::where('customer_id', $CusID)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                Alert::error('Transaksi Ditolak', "Hutang Anda Sudah Banyak, Bayar Dulu !");
                return redirect('/customers/dashboard');
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
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cus/transaksi/TL/1');
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
                $mutasi->username = session()->get('dataLoginCustomers')['username'];
                $mutasi->jenis_transaksi = 'Token Listrik';
                $mutasi->code = $request->produk;
                $mutasi->idcust = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                $mutasi->note = "Pembelian Token Listrik " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk Token Listrik " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk Token Listrik " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = session()->get('dataLoginCustomers')['id'];
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    Alert::success('Transaksi Berhasil', 'Coin Bertambah 1.000');
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = session()->get('dataLoginCustomers')['username'];
                $getNama = session()->get('dataLoginCustomers')['name'];
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx


                return redirect('/cus/transaksi/TL/1');
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
            $idCustomer = session()->get('dataLoginCustomers')['id'];
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {
                $saldoNew = $dompetSekarang - $harga;
                $Customer->saldo = $saldoNew;
                $Customer->save();
                // end pengurangan dompet

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
                    Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                    return redirect('/cus/transaksi/TL/1');
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
                    $mutasi->username = session()->get('dataLoginCustomers')['username'];
                    $mutasi->jenis_transaksi = 'Token Listrik';
                    $mutasi->code = $request->produk;
                    $mutasi->idcust = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $mutasi->note = "Pembelian Token Listrik " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $getUsername = session()->get('dataLoginCustomers')['username'];
                    $getNama = session()->get('dataLoginCustomers')['name'];
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx

                    return redirect('/cus/transaksi/TL/1');
                }
            } elseif ($dompetSekarang < $harga) {
                Alert::error('Transaksi Ditolak!', 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang));
                return redirect('/cus/transaksi/TL/1');
            }
        } else {
            return redirect('/cus/transaksi/TL/1');
        }
    }

    public function transaksiTLBukti($trxid_api)
    {
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
        // return $GetPrabayarHistory;
        // end Get status Trx

        if ($GetPrabayarHistory == null) {
            // perubahan mutasi
            $mutasi = Mutations::where('trxid_api', $trxid_api)->get()->first();
            $mutasi->status = "KADALUARSA";
            $mutasi->save();
            Alert::error('Transaksi Lama', 'Bukti transaksi telah dihapus');
            return back()->withInput();
        }
        return view('customers.pages.transaksiTLBukti', compact('GetPrabayarHistory', 'trxid_api'));
    }


    public function transaksiBank1()
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
        $decodeResponseGetPrabayarOperator = json_decode($responseGetPrabayarOperator)->responseData;
        // end Get Prabayar Operator
        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'Transfer Bank')->where('username', $username)->get()->sortByDesc('created_at');
        return view('customers.pages.transaksiBank1', compact('mutasi', 'decodeResponseGetPrabayarOperator'));
    }

    public function transaksiBank2(Request $request)
    {
        // cek provider
        $phone = $request->no_hp;
        $idProduk = $request->j_bank;

        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$idProduk",
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
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'Transfer Bank')->where('username', $username)->get()->sortByDesc('created_at');
        $saldo = Customers::where('username', $username)->get()->first();
        return view('customers.pages.transaksiBank2', compact('mutasi', 'phone', 'GetPrabayarProduct', 'saldo', 'idProduk'));
    }

    public function transaksiBank3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $CusID = session()->get('dataLoginCustomers')['id'];
            $hutangSaatIni = Hutang::where('customer_id', $CusID)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                Alert::error('Transaksi Ditolak', "Hutang Anda Sudah Banyak, Bayar Dulu !");
                return redirect('/customers/dashboard');
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
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cus/transaksi/Bank/1');
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
                $mutasi->username = session()->get('dataLoginCustomers')['username'];
                $mutasi->jenis_transaksi = 'Transfer Bank';
                $mutasi->code = $request->produk;
                $mutasi->idcust = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = $GetPrabayarHistory['price'] + 4500;
                $mutasi->note = "Transaksi Transfer Bank " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 4500;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Transaksi Transfer Bank " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 4500;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Transaksi Transfer Bank " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = session()->get('dataLoginCustomers')['id'];
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    Alert::success('Transaksi Berhasil', 'Coin Bertambah 1.000');
                }
                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = session()->get('dataLoginCustomers')['username'];
                $getNama = session()->get('dataLoginCustomers')['name'];
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
                return redirect('/cus/transaksi/Bank/1');
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
            $harga = $produkIni[0]['product_price'] + 4500;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = session()->get('dataLoginCustomers')['id'];
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {
                $saldoNew = $dompetSekarang - $harga;
                $Customer->saldo = $saldoNew;
                $Customer->save();
                // end pengurangan dompet

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
                    Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                    return redirect('/cus/transaksi/Bank/1');
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
                    $mutasi->username = session()->get('dataLoginCustomers')['username'];
                    $mutasi->jenis_transaksi = 'Transfer Bank';
                    $mutasi->code = $request->produk;
                    $mutasi->idcust = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = $GetPrabayarHistory['price'] + 4500;
                    $mutasi->note = "Transaksi Transfer Bank " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $getUsername = session()->get('dataLoginCustomers')['username'];
                    $getNama = session()->get('dataLoginCustomers')['name'];
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx
                    return redirect('/cus/transaksi/Bank/1');
                }
            } elseif ($dompetSekarang < $harga) {
                Alert::error('Transaksi Ditolak!', 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang));
                return redirect('/cus/transaksi/Bank/1');
            }
        } else {
            return redirect('/cus/transaksi/Bank/1');
        }
    }

    public function transaksiDANA1()
    {
        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'DANA')->where('username', $username)->get()->sortByDesc('created_at');
        return view('customers.pages.transaksiDANA1', compact('mutasi'));
    }

    public function transaksiDANA2(Request $request)
    {
        // cek provider
        $phone = $request->no_hp;
        $idProduk = "UDDNA";
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$idProduk",
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
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'DANA')->where('username', $username)->get()->sortByDesc('created_at');
        $saldo = Customers::where('username', $username)->get()->first();
        return view('customers.pages.transaksiDANA2', compact('mutasi', 'phone', 'GetPrabayarProduct', 'saldo', 'idProduk'));
    }

    public function transaksiDANA3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $CusID = session()->get('dataLoginCustomers')['id'];
            $hutangSaatIni = Hutang::where('customer_id', $CusID)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                Alert::error('Transaksi Ditolak', "Hutang Anda Sudah Banyak, Bayar Dulu !");
                return redirect('/customers/dashboard');
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
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cus/transaksi/DANA/1');
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
                $mutasi->username = session()->get('dataLoginCustomers')['username'];
                $mutasi->jenis_transaksi = 'DANA';
                $mutasi->code = $request->produk;
                $mutasi->idcust = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                $mutasi->note = "Pembelian DANA " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk DANA " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk DANA " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = session()->get('dataLoginCustomers')['id'];
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    Alert::success('Transaksi Berhasil', 'Coin Bertambah 1.000');
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = session()->get('dataLoginCustomers')['username'];
                $getNama = session()->get('dataLoginCustomers')['name'];
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx
                return redirect('/cus/transaksi/DANA/1');
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
            $idCustomer = session()->get('dataLoginCustomers')['id'];
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {
                $saldoNew = $dompetSekarang - $harga;
                $Customer->saldo = $saldoNew;
                $Customer->save();
                // end pengurangan dompet

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
                    Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                    return redirect('/cus/transaksi/DANA/1');
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
                    $mutasi->username = session()->get('dataLoginCustomers')['username'];
                    $mutasi->jenis_transaksi = 'DANA';
                    $mutasi->code = $request->produk;
                    $mutasi->idcust = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $mutasi->note = "Pembelian DANA " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();


                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $getUsername = session()->get('dataLoginCustomers')['username'];
                    $getNama = session()->get('dataLoginCustomers')['name'];
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx

                    return redirect('/cus/transaksi/DANA/1');
                }
            } elseif ($dompetSekarang < $harga) {
                Alert::error('Transaksi Ditolak!', 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang));
                return redirect('/cus/transaksi/DANA/1');
            }
        } else {
            return redirect('/cus/transaksi/DANA/1');
        }
    }


    public function transaksiOVO1()
    {
        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'OVO')->where('username', $username)->get()->sortByDesc('created_at');
        return view('customers.pages.transaksiOVO1', compact('mutasi'));
    }

    public function transaksiOVO2(Request $request)
    {
        // cek provider
        $phone = $request->no_hp;
        $idProduk = "UDOVO";
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$idProduk",
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
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'OVO')->where('username', $username)->get()->sortByDesc('created_at');
        $saldo = Customers::where('username', $username)->get()->first();
        return view('customers.pages.transaksiOVO2', compact('mutasi', 'phone', 'GetPrabayarProduct', 'saldo', 'idProduk'));
    }

    public function transaksiOVO3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $CusID = session()->get('dataLoginCustomers')['id'];
            $hutangSaatIni = Hutang::where('customer_id', $CusID)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                Alert::error('Transaksi Ditolak', "Hutang Anda Sudah Banyak, Bayar Dulu !");
                return redirect('/customers/dashboard');
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
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cus/transaksi/OVO/1');
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
                $mutasi->username = session()->get('dataLoginCustomers')['username'];
                $mutasi->jenis_transaksi = 'OVO';
                $mutasi->code = $request->produk;
                $mutasi->idcust = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                $mutasi->note = "Pembelian OVO " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk OVO " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk OVO " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = session()->get('dataLoginCustomers')['id'];
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    Alert::success('Transaksi Berhasil', 'Coin Bertambah 1.000');
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = session()->get('dataLoginCustomers')['username'];
                $getNama = session()->get('dataLoginCustomers')['name'];
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx


                return redirect('/cus/transaksi/OVO/1');
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
            $harga = $produkIni[0]['product_price'] + 3000;
            // end Get Prabayar Product

            // pengurangan dompet
            $idCustomer = session()->get('dataLoginCustomers')['id'];
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {
                $saldoNew = $dompetSekarang - $harga;
                $Customer->saldo = $saldoNew;
                $Customer->save();
                // end pengurangan dompet

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
                    Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                    return redirect('/cus/transaksi/OVO/1');
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
                    $mutasi->username = session()->get('dataLoginCustomers')['username'];
                    $mutasi->jenis_transaksi = 'OVO';
                    $mutasi->code = $request->produk;
                    $mutasi->idcust = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name']) + 2000;
                    $mutasi->note = "Pembelian OVO " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();


                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $getUsername = session()->get('dataLoginCustomers')['username'];
                    $getNama = session()->get('dataLoginCustomers')['name'];
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx

                    return redirect('/cus/transaksi/OVO/1');
                }
            } elseif ($dompetSekarang < $harga) {
                Alert::error('Transaksi Ditolak!', 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang));
                return redirect('/cus/transaksi/OVO/1');
            }
        } else {
            return redirect('/cus/transaksi/OVO/1');
        }
    }


    public function transaksiShopeePay1()
    {
        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'ShopeePay')->where('username', $username)->get()->sortByDesc('created_at');
        return view('customers.pages.transaksiShopeePay1', compact('mutasi'));
    }

    public function transaksiShopeePay2(Request $request)
    {
        // cek provider
        $phone = $request->no_hp;
        $idProduk = "UDSHP";
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$idProduk",
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
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'ShopeePay')->where('username', $username)->get()->sortByDesc('created_at');
        $saldo = Customers::where('username', $username)->get()->first();
        return view('customers.pages.transaksiShopeePay2', compact('mutasi', 'phone', 'GetPrabayarProduct', 'saldo', 'idProduk'));
    }

    public function transaksiShopeePay3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $CusID = session()->get('dataLoginCustomers')['id'];
            $hutangSaatIni = Hutang::where('customer_id', $CusID)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                Alert::error('Transaksi Ditolak', "Hutang Anda Sudah Banyak, Bayar Dulu !");
                return redirect('/customers/dashboard');
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
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cus/transaksi/ShopeePay/1');
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
                $mutasi->username = session()->get('dataLoginCustomers')['username'];
                $mutasi->jenis_transaksi = 'ShopeePay';
                $mutasi->code = $request->produk;
                $mutasi->idcust = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                $mutasi->note = "Pembelian ShopeePay " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk ShopeePay " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk ShopeePay " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = session()->get('dataLoginCustomers')['id'];
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    Alert::success('Transaksi Berhasil', 'Coin Bertambah 1.000');
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = session()->get('dataLoginCustomers')['username'];
                $getNama = session()->get('dataLoginCustomers')['name'];
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx

                return redirect('/cus/transaksi/ShopeePay/1');
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
            $idCustomer = session()->get('dataLoginCustomers')['id'];
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {
                $saldoNew = $dompetSekarang - $harga;
                $Customer->saldo = $saldoNew;
                $Customer->save();
                // end pengurangan dompet

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
                    Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                    return redirect('/cus/transaksi/ShopeePay/1');
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
                    $mutasi->username = session()->get('dataLoginCustomers')['username'];
                    $mutasi->jenis_transaksi = 'ShopeePay';
                    $mutasi->code = $request->produk;
                    $mutasi->idcust = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                    $mutasi->note = "Pembelian ShopeePay " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $getUsername = session()->get('dataLoginCustomers')['username'];
                    $getNama = session()->get('dataLoginCustomers')['name'];
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx

                    return redirect('/cus/transaksi/ShopeePay/1');
                }
            } elseif ($dompetSekarang < $harga) {
                Alert::error('Transaksi Ditolak!', 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang));
                return redirect('/cus/transaksi/ShopeePay/1');
            }
        } else {
            return redirect('/cus/transaksi/ShopeePay/1');
        }
    }


    public function transaksiGoPay1()
    {
        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'GoPay')->where('username', $username)->get()->sortByDesc('created_at');
        return view('customers.pages.transaksiGoPay1', compact('mutasi'));
    }

    public function transaksiGoPay2(Request $request)
    {
        // cek provider
        $phone = $request->no_hp;
        $idProduk = "UDGP";
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$idProduk",
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
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'GoPay')->where('username', $username)->get()->sortByDesc('created_at');
        $saldo = Customers::where('username', $username)->get()->first();
        return view('customers.pages.transaksiGoPay2', compact('mutasi', 'phone', 'GetPrabayarProduct', 'saldo', 'idProduk'));
    }

    public function transaksiGoPay3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $CusID = session()->get('dataLoginCustomers')['id'];
            $hutangSaatIni = Hutang::where('customer_id', $CusID)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                Alert::error('Transaksi Ditolak', "Hutang Anda Sudah Banyak, Bayar Dulu !");
                return redirect('/customers/dashboard');
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
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cus/transaksi/GoPay/1');
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
                $mutasi->username = session()->get('dataLoginCustomers')['username'];
                $mutasi->jenis_transaksi = 'GoPay';
                $mutasi->code = $request->produk;
                $mutasi->idcust = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                $mutasi->note = "Pembelian GoPay " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk GoPay " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk GoPay " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = session()->get('dataLoginCustomers')['id'];
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    Alert::success('Transaksi Berhasil', 'Coin Bertambah 1.000');
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = session()->get('dataLoginCustomers')['username'];
                $getNama = session()->get('dataLoginCustomers')['name'];
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx

                return redirect('/cus/transaksi/GoPay/1');
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
            $idCustomer = session()->get('dataLoginCustomers')['id'];
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {
                $saldoNew = $dompetSekarang - $harga;
                $Customer->saldo = $saldoNew;
                $Customer->save();
                // end pengurangan dompet

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
                    Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                    return redirect('/cus/transaksi/GoPay/1');
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
                    $mutasi->username = session()->get('dataLoginCustomers')['username'];
                    $mutasi->jenis_transaksi = 'GoPay';
                    $mutasi->code = $request->produk;
                    $mutasi->idcust = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                    $mutasi->note = "Pembelian GoPay " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $getUsername = session()->get('dataLoginCustomers')['username'];
                    $getNama = session()->get('dataLoginCustomers')['name'];
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx

                    return redirect('/cus/transaksi/GoPay/1');
                }
            } elseif ($dompetSekarang < $harga) {
                Alert::error('Transaksi Ditolak!', 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang));
                return redirect('/cus/transaksi/GoPay/1');
            }
        } else {
            return redirect('/cus/transaksi/GoPay/1');
        }
    }



    public function transaksiLinkAja1()
    {
        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'LinkAja')->where('username', $username)->get()->sortByDesc('created_at');
        return view('customers.pages.transaksiLinkAja1', compact('mutasi'));
    }

    public function transaksiLinkAja2(Request $request)
    {
        // cek provider
        $phone = $request->no_hp;
        $idProduk = "UDGP";
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$idProduk",
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
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'LinkAja')->where('username', $username)->get()->sortByDesc('created_at');
        $saldo = Customers::where('username', $username)->get()->first();
        return view('customers.pages.transaksiLinkAja2', compact('mutasi', 'phone', 'GetPrabayarProduct', 'saldo', 'idProduk'));
    }

    public function transaksiLinkAja3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $CusID = session()->get('dataLoginCustomers')['id'];
            $hutangSaatIni = Hutang::where('customer_id', $CusID)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                Alert::error('Transaksi Ditolak', "Hutang Anda Sudah Banyak, Bayar Dulu !");
                return redirect('/customers/dashboard');
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
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cus/transaksi/LinkAja/1');
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
                $mutasi->username = session()->get('dataLoginCustomers')['username'];
                $mutasi->jenis_transaksi = 'LinkAja';
                $mutasi->code = $request->produk;
                $mutasi->idcust = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                $mutasi->note = "Pembelian LinkAja " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk LinkAja " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk LinkAja " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = session()->get('dataLoginCustomers')['id'];
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    Alert::success('Transaksi Berhasil', 'Coin Bertambah 1.000');
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = session()->get('dataLoginCustomers')['username'];
                $getNama = session()->get('dataLoginCustomers')['name'];
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx

                return redirect('/cus/transaksi/LinkAja/1');
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
            $idCustomer = session()->get('dataLoginCustomers')['id'];
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {
                $saldoNew = $dompetSekarang - $harga;
                $Customer->saldo = $saldoNew;
                $Customer->save();
                // end pengurangan dompet

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
                    Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                    return redirect('/cus/transaksi/LinkAja/1');
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
                    $mutasi->username = session()->get('dataLoginCustomers')['username'];
                    $mutasi->jenis_transaksi = 'LinkAja';
                    $mutasi->code = $request->produk;
                    $mutasi->idcust = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                    $mutasi->note = "Pembelian LinkAja " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $getUsername = session()->get('dataLoginCustomers')['username'];
                    $getNama = session()->get('dataLoginCustomers')['name'];
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx

                    return redirect('/cus/transaksi/LinkAja/1');
                }
            } elseif ($dompetSekarang < $harga) {
                Alert::error('Transaksi Ditolak!', 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang));
                return redirect('/cus/transaksi/LinkAja/1');
            }
        } else {
            return redirect('/cus/transaksi/LinkAja/1');
        }
    }


    public function transaksiWifiId1()
    {
        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'Wifi ID')->where('username', $username)->get()->sortByDesc('created_at');
        return view('customers.pages.transaksiWifiId1', compact('mutasi'));
    }

    public function transaksiWifiId2(Request $request)
    {
        // cek provider
        $phone = $request->no_hp;
        $idProduk = "WIFID";
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$idProduk",
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
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'Wifi ID')->where('username', $username)->get()->sortByDesc('created_at');
        $saldo = Customers::where('username', $username)->get()->first();
        return view('customers.pages.transaksiWifiId2', compact('mutasi', 'phone', 'GetPrabayarProduct', 'saldo', 'idProduk'));
    }

    public function transaksiWifiId3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $CusID = session()->get('dataLoginCustomers')['id'];
            $hutangSaatIni = Hutang::where('customer_id', $CusID)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                Alert::error('Transaksi Ditolak', "Hutang Anda Sudah Banyak, Bayar Dulu !");
                return redirect('/customers/dashboard');
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
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cus/transaksi/wifi-id/1');
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
                $mutasi->username = session()->get('dataLoginCustomers')['username'];
                $mutasi->jenis_transaksi = 'Wifi ID';
                $mutasi->code = $request->produk;
                $mutasi->idcust = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                $mutasi->note = "Pembelian Wifi ID " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk Wifi ID " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Produk Wifi ID " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = session()->get('dataLoginCustomers')['id'];
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    Alert::success('Transaksi Berhasil', 'Coin Bertambah 1.000');
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = session()->get('dataLoginCustomers')['username'];
                $getNama = session()->get('dataLoginCustomers')['name'];
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx

                return redirect('/cus/transaksi/wifi-id/1');
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
            $idCustomer = session()->get('dataLoginCustomers')['id'];
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {
                $saldoNew = $dompetSekarang - $harga;
                $Customer->saldo = $saldoNew;
                $Customer->save();
                // end pengurangan dompet

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
                    Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                    return redirect('/cus/transaksi/wifi-id/1');
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
                    $mutasi->username = session()->get('dataLoginCustomers')['username'];
                    $mutasi->jenis_transaksi = 'Wifi ID';
                    $mutasi->code = $request->produk;
                    $mutasi->idcust = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                    $mutasi->note = "Pembelian Wifi ID " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $getUsername = session()->get('dataLoginCustomers')['username'];
                    $getNama = session()->get('dataLoginCustomers')['name'];
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx

                    return redirect('/cus/transaksi/wifi-id/1');
                }
            } elseif ($dompetSekarang < $harga) {
                Alert::error('Transaksi Ditolak!', 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang));
                return redirect('/cus/transaksi/wifi-id/1');
            }
        } else {
            return redirect('/cus/transaksi/wifi-id/1');
        }
    }

    public function transaksivgml2()
    {
        // cek provider
        $idProduk = "VGML";
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$idProduk",
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
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'Diamond Mobile Legend')->where('username', $username)->get()->sortByDesc('created_at');
        $saldo = Customers::where('username', $username)->get()->first();
        return view('customers.pages.transaksivgml2', compact('mutasi', 'GetPrabayarProduct', 'saldo', 'idProduk'));
    }

    public function transaksivgml3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $CusID = session()->get('dataLoginCustomers')['id'];
            $hutangSaatIni = Hutang::where('customer_id', $CusID)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                Alert::error('Transaksi Ditolak', "Hutang Anda Sudah Banyak, Bayar Dulu !");
                return redirect('/customers/dashboard');
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
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cus/transaksi/vgml/2');
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
                $mutasi->username = session()->get('dataLoginCustomers')['username'];
                $mutasi->jenis_transaksi = 'Diamond Mobile Legend';
                $mutasi->code = $request->produk;
                $mutasi->idcust = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                $mutasi->note = "Pembelian Diamond Mobile Legend " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Diamond Mobile Legend " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Diamond Mobile Legend " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = session()->get('dataLoginCustomers')['id'];
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    Alert::success('Transaksi Berhasil', 'Coin Bertambah 1.000');
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = session()->get('dataLoginCustomers')['username'];
                $getNama = session()->get('dataLoginCustomers')['name'];
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx

                return redirect('/cus/transaksi/vgml/2');
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
            $idCustomer = session()->get('dataLoginCustomers')['id'];
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {
                $saldoNew = $dompetSekarang - $harga;
                $Customer->saldo = $saldoNew;
                $Customer->save();
                // end pengurangan dompet

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
                    Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                    return redirect('/cus/transaksi/vgml/2');
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
                    $mutasi->username = session()->get('dataLoginCustomers')['username'];
                    $mutasi->jenis_transaksi = 'Diamond Mobile Legend';
                    $mutasi->code = $request->produk;
                    $mutasi->idcust = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                    $mutasi->note = "Pembelian Diamond Mobile Legend " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $getUsername = session()->get('dataLoginCustomers')['username'];
                    $getNama = session()->get('dataLoginCustomers')['name'];
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx

                    return redirect('/cus/transaksi/vgml/2');
                }
            } elseif ($dompetSekarang < $harga) {
                Alert::error('Transaksi Ditolak!', 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang));
                return redirect('/cus/transaksi/vgml/2');
            }
        } else {
            return redirect('/cus/transaksi/vgml/2');
        }
    }

    public function transaksivgff2()
    {
        // cek provider
        $idProduk = "VGFF";
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=$idProduk",
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
        $decodeResponseGetPrabayarProduct = json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


        $username = session()->get('dataLoginCustomers')['username'];
        $mutasi = Mutations::where('jenis_transaksi', 'Diamond Free Fire')->where('username', $username)->get()->sortByDesc('created_at');
        $saldo = Customers::where('username', $username)->get()->first();
        return view('customers.pages.transaksivgff2', compact('mutasi', 'GetPrabayarProduct', 'saldo', 'idProduk'));
    }

    public function transaksivgff3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // cek hutang
            $CusID = session()->get('dataLoginCustomers')['id'];
            $hutangSaatIni = Hutang::where('customer_id', $CusID)->sum('sisa');
            if ($hutangSaatIni >= 300000) {
                Alert::error('Transaksi Ditolak', "Hutang Anda Sudah Banyak, Bayar Dulu !");
                return redirect('/customers/dashboard');
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
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cus/transaksi/vgff/2');
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
                $mutasi->username = session()->get('dataLoginCustomers')['username'];
                $mutasi->jenis_transaksi = 'Diamond Free Fire';
                $mutasi->code = $request->produk;
                $mutasi->idcust = $request->no_hp;
                $mutasi->status = $GetPrabayarHistory['status'];
                $mutasi->trxid_api = $trxid_api;
                $mutasi->harga_normal = $GetPrabayarHistory['price'];
                $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                $mutasi->note = "Pembelian Diamond Free Fire " . $GetPrabayarHistory['product_name'];
                $mutasi->save();

                if ($GetPrabayarHistory['status'] == "PENDING" or $GetPrabayarHistory['status'] == "PROCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Diamond Free Fire " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();
                } elseif ($GetPrabayarHistory['status'] == "SUCCESS") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang->customer_id = session()->get('dataLoginCustomers')['id'];
                    $hutang->trxid_api = $trxid_api;
                    $hutang->nominal = $GetPrabayarHistory['price'] + 2000;
                    $hutang->sisa = $hutang->nominal;
                    $hutang->keterangan = "Pembelian Diamond Free Fire " . $GetPrabayarHistory['product_id'] . $GetPrabayarHistory['product_name'];
                    $hutang->save();

                    // menambah point
                    $idCustomer = session()->get('dataLoginCustomers')['id'];
                    $UpdatePointCustomer = Customers::where('id', $idCustomer)->first();
                    $UpdatePointCustomer->point = $UpdatePointCustomer->point + 1000;
                    $UpdatePointCustomer->save();
                    Alert::success('Transaksi Berhasil', 'Coin Bertambah 1.000');
                }

                // telegram_bot_trx
                $chat_id = "360835825";
                $getUsername = session()->get('dataLoginCustomers')['username'];
                $getNama = session()->get('dataLoginCustomers')['name'];
                $getMessage = $mutasi->note;
                $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                // telegram_bot_trx

                return redirect('/cus/transaksi/vgff/2');
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
            $idCustomer = session()->get('dataLoginCustomers')['id'];
            $Customer = Customers::where('id', $idCustomer)->get()->first();
            $dompetSekarang = $Customer->saldo;
            if ($dompetSekarang >= $harga) {
                $saldoNew = $dompetSekarang - $harga;
                $Customer->saldo = $saldoNew;
                $Customer->save();
                // end pengurangan dompet

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
                    Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                    return redirect('/cus/transaksi/vgff/2');
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
                    $mutasi->username = session()->get('dataLoginCustomers')['username'];
                    $mutasi->jenis_transaksi = 'Diamond Free Fire';
                    $mutasi->code = $request->produk;
                    $mutasi->idcust = $request->no_hp;
                    $mutasi->status = $GetPrabayarHistory['status'];
                    $mutasi->trxid_api = $trxid_api;
                    $mutasi->harga_normal = $GetPrabayarHistory['price'];
                    $mutasi->harga_jual = $GetPrabayarHistory['price'] + 2000;
                    $mutasi->note = "Pembelian Diamond Free Fire " . $GetPrabayarHistory['product_name'];;
                    $mutasi->save();

                    // telegram_bot_trx
                    $chat_id = "360835825";
                    $getUsername = session()->get('dataLoginCustomers')['username'];
                    $getNama = session()->get('dataLoginCustomers')['name'];
                    $getMessage = $mutasi->note;
                    $messageTemplate = "Nama : $getNama%0AUsername : $getUsername%0A%0A%0A$getMessage";
                    $token = "5289156712:AAHGgFmHb97QIuSrSFOzuF9enJQ0wMIR4ow";
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=$messageTemplate";
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($curl);
                    curl_close($curl);
                    // telegram_bot_trx

                    return redirect('/cus/transaksi/vgff/2');
                }
            } elseif ($dompetSekarang < $harga) {
                Alert::error('Transaksi Ditolak!', 'Saldo Anda tidak cukup, saat ini saldo anda Rp. ' . number_format($dompetSekarang));
                return redirect('/cus/transaksi/vgff/2');
            }
        } else {
            return redirect('/cus/transaksi/vgff/2');
        }
    }
}
