<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mutations;
use App\Models\Hutang;
use App\Models\Customers;
use Alert;

class transaksiCSController extends Controller
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
                'Authorization: '.config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
        $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
        $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
        // return $GetPrabayarHistory;
        // end Get status Trx 

        if ($GetPrabayarHistory['status'] != "PENDING") {    
            // mencatat ke mutasi
            $mutasi = Mutations::where('trxid_api', $id)->get()->first();
            $mutasi -> status = $GetPrabayarHistory['status'];
            $mutasi -> note = $GetPrabayarHistory['message'];
            $mutasi->save();
            // menghapus hutang
            if ($GetPrabayarHistory['status'] == "FAILED") {
                $hutang = Hutang::where('trxid_api', $id)->get()->first();
                if ($hutang != null) {
                    $hutang->delete();
                }
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
                'Authorization: '.config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
        $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
        $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
        // return $GetPrabayarHistory;
        // end Get status Trx 
        return view('cs.pages.transaksiBukti', compact('GetPrabayarHistory', 'trxid_api'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function transaksiPIU1()
    {
        $mutasi = Mutations::where('jenis_transaksi', 'Pulsa Isi Ulang')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiPIU1', compact('mutasi'));
    }

    public function transaksiPIU2(Request $request)
    {
        // cek provider
        $phone=$request->no_hp;
        $phone4=substr(trim($phone), 0, 4); //memotong no hp 4 awal
        if (in_array($phone4, explode(',', '0852,0853,0811,0812,0813,0821,0822,0823,0851'))) {
            $idProduk = 'PIUTSL';
        }elseif (in_array($phone4, explode(',', '0831,0832,0833,0838'))) {
            $idProduk = 'PIUAX';
        }elseif (in_array($phone4, explode(',', '0859,0877,0878,0817,0818,0819'))) {
            $idProduk = 'PIUXL';
        }elseif (in_array($phone4, explode(',', '0814,0815,0816,0855,0856,0857,0858,0813'))) {
            $idProduk = 'PIUIST';
        }elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
            $idProduk = 'PIUTR';
        }elseif (in_array($phone4, explode(',', '0881,0882,0883,0884,0885,0886,0887,0888,0889'))) {
            $idProduk = 'PIUSF';
        }elseif (in_array($phone4, explode(',', '0851,08515'))) {
            $idProduk = 'PIUBY';
        }elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879,0831,0838,08591,0817,0818,0819,0859,0877,0878,0879,0859,0832'))) {
            $idProduk = 'PIUAXL';
        }elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879,0831,0838,08591,0817,0818,0819,0859,0877,0878,0879,0859,0832'))) {
            $idProduk = 'PIUAXLP';
        }elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879,0831,0838,08591,0817,0818,0819,0859,0877,0878,0879,0859,0832'))) {
            $idProduk = 'PIUAXLSP';
        }elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
            $idProduk = 'PIUTRP';
        }elseif (in_array($phone4, explode(',', '0814,0815,0816,0855,0856,0857,0858'))) {
            $idProduk = 'PIUISTP';
        }elseif (in_array($phone4, explode(',', '0814,0815,0816,0855,0856,0857,0858,0813'))) {
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
                'Authorization: '.config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


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

        $customer = Customers::all();
        $mutasi = Mutations::where('jenis_transaksi', 'Pulsa Isi Ulang')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiPIU2', compact('customer', 'mutasi', 'phone', 'GetPrabayarProduct', 'saldo'));
    }

    public function transaksiPIU3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // membuat trxid_api
            $trxid_api = date('Ymd').rand();

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_hp,
                "product_id"=>$request->produk,
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/PIU/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Pulsa Isi Ulang';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_hp;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                if ($GetPrabayarHistory['status'] != "FAILED") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang -> customer_id = $request->customer;
                    $hutang -> trxid_api = $trxid_api;
                    $hutang -> nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name'])+2000;
                    $hutang -> sisa = $hutang -> nominal;
                    $hutang -> keterangan = "Pembelian Produk ".$GetPrabayarHistory['product_id'].$GetPrabayarHistory['product_name'];
                    $hutang -> save();
                }
                return redirect('/cs/transaksi/PIU/1');
            }
        }elseif($request->metode_pembayaran == "Dompet") {
            // membuat trxid_api
            $trxid_api = date('Ymd').rand();

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_hp,
                "product_id"=>$request->produk,
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/PIU/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Pulsa Isi Ulang';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_hp;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                return redirect('/cs/transaksi/PIU/1');
            }
        }else{
            return redirect('/cs/transaksi/PIU/1');
        }
    }

    public function transaksiPIUBukti($trxid_api)
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
                'Authorization: '.config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
        $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
        $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
        // return $GetPrabayarHistory;
        // end Get status Trx 
        return view('cs.pages.transaksiPIUBukti', compact('GetPrabayarHistory', 'trxid_api'));
    }


    public function transaksiPD1()
    {
        $mutasi = Mutations::where('jenis_transaksi', 'Paket Data Internet')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiPD1', compact('mutasi'));
    }

    public function transaksiPD2(Request $request)
    {
        // cek provider
        $phone=$request->no_hp;
        $phone4=substr(trim($phone), 0, 4); //memotong no hp 4 awal
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
        }elseif (in_array($phone4, explode(',', '0831,0832,0833,0838'))) {
            $idProduk = 'PDAXBL';
        // }elseif (in_array($phone4, explode(',', '0831,0838'))) {
        //     $idProduk = 'PDAXMM';
        // }elseif (in_array($phone4, explode(',', '0831,0838,0859'))) {
        //     $idProduk = 'PDAXVC';
        // }elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879'))) {
        //     $idProduk = 'PDXLHR';
        // }elseif (in_array($phone4, explode(',', '0817,0818,0819,0859,0877,0878,0879'))) {
        //     $idProduk = 'PDXLMG';
        }elseif (in_array($phone4, explode(',', '0859,0877,0878,0817,0818,0819'))) {
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
        }elseif (in_array($phone4, explode(',', '0814,0815,0816,0855,0856,0857,0858'))) {
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
        }elseif (in_array($phone4, explode(',', '0881,0882,0883,0884,0885,0886,0887,0888,0889'))) {
            $idProduk = 'PDSFBL';
        // }elseif (in_array($phone4, explode(',', '0881,0882,0883,0884,0885,0886,0887,0888,0889'))) {
        //     $idProduk = 'PDSFMF';
        // }elseif (in_array($phone4, explode(',', '0881,0882,0883,0884,0885,0886,0887,0888,0889'))) {
        //     $idProduk = 'PDSFVC';
        // }elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
        //     $idProduk = 'PDTRHR';
        // }elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
        //     $idProduk = 'PDTRMG';
        }elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
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
        }elseif (in_array($phone4, explode(',', '0852,0853,0811,0812,0813,0821,0822,0823,0851'))) {
            $idProduk = 'PDTSLUNLI';
        }elseif (in_array($phone4, explode(',', '0896,0897,0898,0899,0895'))) {
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
                'Authorization: '.config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


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

        $customer = Customers::all();
        $mutasi = Mutations::where('jenis_transaksi', 'Paket Data Internet')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiPD2', compact('customer', 'mutasi', 'phone', 'GetPrabayarProduct', 'saldo'));
    }

    public function transaksiPD3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_hp,
                "product_id"=>$request->produk,
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/PD/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Paket Data Internet';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_hp;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                if ($GetPrabayarHistory['status'] != "FAILED") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang -> customer_id = $request->customer;
                    $hutang -> trxid_api = $trxid_api;
                    $hutang -> nominal = $GetPrabayarHistory['price']+5000;
                    $hutang -> sisa = $hutang -> nominal;
                    $hutang -> keterangan = "Pembelian Produk ".$GetPrabayarHistory['product_id'].$GetPrabayarHistory['product_name'];
                    $hutang -> save();
                }
                return redirect('/cs/transaksi/PD/1');
            }
        }elseif($request->metode_pembayaran == "Dompet") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_hp,
                "product_id"=>$request->produk,
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/PD/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Paket Data Internet';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_hp;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                return redirect('/cs/transaksi/PD/1');
            }
        }else{
            return redirect('/cs/transaksi/PD/1');
        }
    }

    public function transaksiPDBukti($trxid_api)
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
                'Authorization: '.config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
        $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
        $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
        // return $GetPrabayarHistory;
        // end Get status Trx 
        return view('cs.pages.transaksiPDBukti', compact('GetPrabayarHistory', 'trxid_api'));
    }


    public function transaksiTL1()
    {
        $mutasi = Mutations::where('jenis_transaksi', 'Token Listrik')->get()->sortByDesc('created_at');
        // Get Prabayar Operator
        $curlGetPrabayarOperator = curl_init();
        curl_setopt_array($curlGetPrabayarOperator, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/operator?product_id=TL",
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
        $responseGetPrabayarOperator = curl_exec($curlGetPrabayarOperator);
        $decodeResponseGetPrabayarOperator=json_decode($responseGetPrabayarOperator)->responseData;
        // end Get Prabayar Operator
        return view('cs.pages.transaksiTL1', compact('mutasi', 'decodeResponseGetPrabayarOperator'));
    }

    public function transaksiTL2(Request $request)
    {
        $no_pelanggan = $request->no_pelanggan;
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=TLOP",
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
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


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

        $customer = Customers::all();
        $mutasi = Mutations::where('jenis_transaksi', 'Token Listrik')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiTL2', compact('customer', 'mutasi', 'no_pelanggan', 'GetPrabayarProduct', 'saldo'));
    }

    public function transaksiTL3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/TL/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // return $GetPrabayarHistory;
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Token Listrik';
                $mutasi -> code = $request->produk;
                $mutasi -> idcust = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                if ($GetPrabayarHistory['status'] != "FAILED") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang -> customer_id = $request->customer;
                    $hutang -> trxid_api = $trxid_api;
                    $hutang -> nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name'])+2000;
                    $hutang -> sisa = $hutang -> nominal;
                    $hutang -> keterangan = $GetPrabayarHistory['message'];
                    $hutang -> save();
                }
                return redirect('/cs/transaksi/TL/1');
            }
        }elseif($request->metode_pembayaran == "Dompet") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/TL/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Token Listrik';
                $mutasi -> code = $request->produk;
                $mutasi -> idcust = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                return redirect('/cs/transaksi/TL/1');
            }
        }else{
            return redirect('/cs/transaksi/TL/1');
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
                'Authorization: '.config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
        $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
        $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
        // return $GetPrabayarHistory;
        // end Get status Trx 
        return view('cs.pages.transaksiTLBukti', compact('GetPrabayarHistory', 'trxid_api'));
    }


    public function transaksiBank1()
    {
        $mutasi = Mutations::where('jenis_transaksi', 'Transfer Bank')->get()->sortByDesc('created_at');
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
                'Authorization: '.config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarOperator = curl_exec($curlGetPrabayarOperator);
        $decodeResponseGetPrabayarOperator=json_decode($responseGetPrabayarOperator)->responseData;
        // end Get Prabayar Operator
        return view('cs.pages.transaksiBank1', compact('mutasi', 'decodeResponseGetPrabayarOperator'));
    }

    public function transaksiBank2(Request $request)
    {
        $idProduk=$request->j_bank;
        $no_rek = $request->no_rek;
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
                'Authorization: '.config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


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

        $customer = Customers::all();
        $mutasi = Mutations::where('jenis_transaksi', 'Transfer Bank')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiBank2', compact('customer', 'mutasi', 'no_rek', 'GetPrabayarProduct', 'saldo'));
    }

    public function transaksiBank3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_rek,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/Bank/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // return $GetPrabayarHistory;
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Transfer Bank';
                $mutasi -> code = $request->produk;
                $mutasi -> idcust = $request->no_rek;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                if ($GetPrabayarHistory['status'] != "FAILED") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang -> customer_id = $request->customer;
                    $hutang -> trxid_api = $trxid_api;
                    $hutang -> nominal = $GetPrabayarHistory['price']+4500;
                    $hutang -> sisa = $hutang -> nominal;
                    $hutang -> keterangan = $GetPrabayarHistory['message'];
                    $hutang -> save();
                }
                return redirect('/cs/transaksi/Bank/1');
            }
        }elseif($request->metode_pembayaran == "Dompet") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_rek,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/Bank/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Transfer Bank';
                $mutasi -> code = $request->produk;
                $mutasi -> idcust = $request->no_rek;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                return redirect('/cs/transaksi/Bank/1');
            }
        }else{
            return redirect('/cs/transaksi/Bank/1');
        }
    }

    public function transaksiBankBukti($trxid_api)
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
                'Authorization: '.config('api.serpul_key_api'),
            ),
        ));
        $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
        $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
        $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
        // return $GetPrabayarHistory;
        // end Get status Trx 
        return view('cs.pages.transaksiBankBukti', compact('GetPrabayarHistory', 'trxid_api'));
    }



    public function transaksiDANA1()
    {
        $mutasi = Mutations::where('jenis_transaksi', 'DANA')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiDANA1', compact('mutasi'));
    }

    public function transaksiDANA2(Request $request)
    {
        $no_pelanggan = $request->no_pelanggan;
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=UDDNA",
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
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


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

        $customer = Customers::all();
        $mutasi = Mutations::where('jenis_transaksi', 'DANA')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiDANA2', compact('customer', 'mutasi', 'no_pelanggan', 'GetPrabayarProduct', 'saldo'));
    }

    public function transaksiDANA3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/DANA/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // return $GetPrabayarHistory;
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'DANA';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                if ($GetPrabayarHistory['status'] != "FAILED") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang -> customer_id = $request->customer;
                    $hutang -> trxid_api = $trxid_api;
                    $hutang -> nominal = $GetPrabayarHistory['price']+2000;
                    $hutang -> sisa = $hutang -> nominal;
                    $hutang -> keterangan = "DANA ".$GetPrabayarHistory['message'];
                    $hutang -> save();
                }
                return redirect('/cs/transaksi/DANA/1');
            }
        }elseif($request->metode_pembayaran == "Dompet") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/DANA/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'DANA';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                return redirect('/cs/transaksi/DANA/1');
            }
        }else{
            return redirect('/cs/transaksi/DANA/1');
        }
    }


    public function transaksiOVO1()
    {
        $mutasi = Mutations::where('jenis_transaksi', 'OVO')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiOVO1', compact('mutasi'));
    }

    public function transaksiOVO2(Request $request)
    {
        $no_pelanggan = $request->no_pelanggan;
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=UDOVO",
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
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


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

        $customer = Customers::all();
        $mutasi = Mutations::where('jenis_transaksi', 'OVO')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiOVO2', compact('customer', 'mutasi', 'no_pelanggan', 'GetPrabayarProduct', 'saldo'));
    }

    public function transaksiOVO3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/OVO/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // return $GetPrabayarHistory;
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'OVO';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                if ($GetPrabayarHistory['status'] != "FAILED") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang -> customer_id = $request->customer;
                    $hutang -> trxid_api = $trxid_api;
                    $hutang -> nominal = preg_replace('/[^0-9]/', '', $GetPrabayarHistory['product_name'])+2000;
                    $hutang -> sisa = $hutang -> nominal;
                    $hutang -> keterangan = "OVO ".$GetPrabayarHistory['message'];
                    $hutang -> save();
                }
                return redirect('/cs/transaksi/OVO/1');
            }
        }elseif($request->metode_pembayaran == "Dompet") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/OVO/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'OVO';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                return redirect('/cs/transaksi/OVO/1');
            }
        }else{
            return redirect('/cs/transaksi/OVO/1');
        }
    }

    public function transaksiShopeePay1()
    {
        $mutasi = Mutations::where('jenis_transaksi', 'ShopeePay')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiShopeePay1', compact('mutasi'));
    }

    public function transaksiShopeePay2(Request $request)
    {
        $no_pelanggan = $request->no_pelanggan;
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=UDSHP",
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
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


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

        $customer = Customers::all();
        $mutasi = Mutations::where('jenis_transaksi', 'ShopeePay')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiShopeePay2', compact('customer', 'mutasi', 'no_pelanggan', 'GetPrabayarProduct', 'saldo'));
    }

    public function transaksiShopeePay3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/ShopeePay/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // return $GetPrabayarHistory;
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'ShopeePay';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                if ($GetPrabayarHistory['status'] != "FAILED") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang -> customer_id = $request->customer;
                    $hutang -> trxid_api = $trxid_api;
                    $hutang -> nominal = $GetPrabayarHistory['price']+2000;
                    $hutang -> sisa = $hutang -> nominal;
                    $hutang -> keterangan = "ShopeePay ".$GetPrabayarHistory['message'];
                    $hutang -> save();
                }
                return redirect('/cs/transaksi/ShopeePay/1');
            }
        }elseif($request->metode_pembayaran == "Dompet") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/ShopeePay/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'ShopeePay';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                return redirect('/cs/transaksi/ShopeePay/1');
            }
        }else{
            return redirect('/cs/transaksi/ShopeePay/1');
        }
    }

    public function transaksiGoPay1()
    {
        $mutasi = Mutations::where('jenis_transaksi', 'GoPay')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiGoPay1', compact('mutasi'));
    }

    public function transaksiGoPay2(Request $request)
    {
        $no_pelanggan = $request->no_pelanggan;
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=UDGP",
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
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


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

        $customer = Customers::all();
        $mutasi = Mutations::where('jenis_transaksi', 'GoPay')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiGoPay2', compact('customer', 'mutasi', 'no_pelanggan', 'GetPrabayarProduct', 'saldo'));
    }

    public function transaksiGoPay3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/GoPay/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // return $GetPrabayarHistory;
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'GoPay';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                if ($GetPrabayarHistory['status'] != "FAILED") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang -> customer_id = $request->customer;
                    $hutang -> trxid_api = $trxid_api;
                    $hutang -> nominal = $GetPrabayarHistory['price']+2000;
                    $hutang -> sisa = $hutang -> nominal;
                    $hutang -> keterangan = "GoPay ".$GetPrabayarHistory['message'];
                    $hutang -> save();
                }
                return redirect('/cs/transaksi/GoPay/1');
            }
        }elseif($request->metode_pembayaran == "Dompet") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/GoPay/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'GoPay';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                return redirect('/cs/transaksi/GoPay/1');
            }
        }else{
            return redirect('/cs/transaksi/GoPay/1');
        }
    }


    public function transaksiLinkAja1()
    {
        $mutasi = Mutations::where('jenis_transaksi', 'LinkAja')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiLinkAja1', compact('mutasi'));
    }

    public function transaksiLinkAja2(Request $request)
    {
        $no_pelanggan = $request->no_pelanggan;
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=UDLA",
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
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


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

        $customer = Customers::all();
        $mutasi = Mutations::where('jenis_transaksi', 'LinkAja')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiLinkAja2', compact('customer', 'mutasi', 'no_pelanggan', 'GetPrabayarProduct', 'saldo'));
    }

    public function transaksiLinkAja3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/LinkAja/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // return $GetPrabayarHistory;
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'LinkAja';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                if ($GetPrabayarHistory['status'] != "FAILED") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang -> customer_id = $request->customer;
                    $hutang -> trxid_api = $trxid_api;
                    $hutang -> nominal = $GetPrabayarHistory['price']+2000;
                    $hutang -> sisa = $hutang -> nominal;
                    $hutang -> keterangan = "LinkAja ".$GetPrabayarHistory['message'];
                    $hutang -> save();
                }
                return redirect('/cs/transaksi/LinkAja/1');
            }
        }elseif($request->metode_pembayaran == "Dompet") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/LinkAja/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'LinkAja';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                return redirect('/cs/transaksi/LinkAja/1');
            }
        }else{
            return redirect('/cs/transaksi/LinkAja/1');
        }
    }


    public function transaksiWifiId1()
    {
        $mutasi = Mutations::where('jenis_transaksi', 'Wifi ID')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiWifiId1', compact('mutasi'));
    }

    public function transaksiWifiId2(Request $request)
    {
        $no_pelanggan = $request->no_pelanggan;
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=WIFID",
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
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


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

        $customer = Customers::all();
        $mutasi = Mutations::where('jenis_transaksi', 'Wifi ID')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksiWifiId2', compact('customer', 'mutasi', 'no_pelanggan', 'GetPrabayarProduct', 'saldo'));
    }

    public function transaksiWifiId3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/wifi-id/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // return $GetPrabayarHistory;
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Wifi ID';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                if ($GetPrabayarHistory['status'] != "FAILED") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang -> customer_id = $request->customer;
                    $hutang -> trxid_api = $trxid_api;
                    $hutang -> nominal = $GetPrabayarHistory['price']+2000;
                    $hutang -> sisa = $hutang -> nominal;
                    $hutang -> keterangan = "Wifi ID ".$GetPrabayarHistory['message'];
                    $hutang -> save();
                }
                return redirect('/cs/transaksi/wifi-id/1');
            }
        }elseif($request->metode_pembayaran == "Dompet") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/wifi-id/1');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Wifi ID';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                return redirect('/cs/transaksi/wifi-id/1');
            }
        }else{
            return redirect('/cs/transaksi/wifi-id/1');
        }
    }

    public function transaksivgml2()
    {
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=VGML",
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
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


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

        $customer = Customers::all();
        $mutasi = Mutations::where('jenis_transaksi', 'Diamond Mobile Legend')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksivgml2', compact('customer', 'mutasi', 'GetPrabayarProduct', 'saldo'));
    }

    public function transaksivgml3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/vgml/2');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // return $GetPrabayarHistory;
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Diamond Mobile Legend';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                if ($GetPrabayarHistory['status'] != "FAILED") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang -> customer_id = $request->customer;
                    $hutang -> trxid_api = $trxid_api;
                    $hutang -> nominal = $GetPrabayarHistory['price']+2000;
                    $hutang -> sisa = $hutang -> nominal;
                    $hutang -> keterangan = "Diamond Mobile Legend ".$GetPrabayarHistory['message'];
                    $hutang -> save();
                }
                return redirect('/cs/transaksi/vgml/2');
            }
        }elseif($request->metode_pembayaran == "Dompet") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/vgml/2');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Diamond Mobile Legend';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                return redirect('/cs/transaksi/vgml/2');
            }
        }else{
            return redirect('/cs/transaksi/vgml/2');
        }
    }

    public function transaksivgff2()
    {
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=VGFF",
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
        $responseGetPrabayarProduct = curl_exec($curlGetPrabayarProduct);
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct->responseData;
        // return $GetPrabayarProduct;
        // end Get Prabayar Product


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

        $customer = Customers::all();
        $mutasi = Mutations::where('jenis_transaksi', 'Diamond Free Fire')->get()->sortByDesc('created_at');
        return view('cs.pages.transaksivgff2', compact('customer', 'mutasi', 'GetPrabayarProduct', 'saldo'));
    }

    public function transaksivgff3(Request $request)
    {
        if ($request->metode_pembayaran == "Hutang") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/vgff/2');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // return $GetPrabayarHistory;
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Diamond Free Fire';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                if ($GetPrabayarHistory['status'] != "FAILED") {
                    // mencatat ke hutang
                    $hutang = new Hutang;
                    $hutang -> customer_id = $request->customer;
                    $hutang -> trxid_api = $trxid_api;
                    $hutang -> nominal = $GetPrabayarHistory['price']+2000;
                    $hutang -> sisa = $hutang -> nominal;
                    $hutang -> keterangan = "Diamond Free Fire ".$GetPrabayarHistory['message'];
                    $hutang -> save();
                }
                return redirect('/cs/transaksi/vgff/2');
            }
        }elseif($request->metode_pembayaran == "Dompet") {
            // membuat trxid_api
            $trxid_api = date('Ymdhis');

            // Post Prabayar
            $dataPostPrabayar = [
                "destination"=>$request->no_pelanggan,
                "product_id"=>$request->produk,
                // "product_id"=>"CEKBRI",
                "ref_id"=>$trxid_api,
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
                    'Authorization: '.config('api.serpul_key_api'),
                ),
            ));
            $responsePostPrabayar = curl_exec($curlPostPrabayar);
            $decodeResponsePostPrabayar=json_decode($responsePostPrabayar, true);
            // end Post Prabayar

            if ($decodeResponsePostPrabayar['responseCode'] == 400) { //failed inquiry
                Alert::error('Terjadi Gangguan', $decodeResponsePostPrabayar['responseMessage']);
                return redirect('/cs/transaksi/vgff/2');
            }else{ //success inquiry

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
                        'Authorization: '.config('api.serpul_key_api'),
                    ),
                ));
                $responseGetPrabayarHistory = curl_exec($curlGetPrabayarHistory);
                $decodeResponseGetPrabayarHistory=json_decode($responseGetPrabayarHistory, 1);
                $GetPrabayarHistory = $decodeResponseGetPrabayarHistory['responseData'];
                // end Get status Trx 

                // mencatat ke mutasi
                $mutasi = new Mutations;
                $mutasi -> username = session()->get('dataLoginCustomerServices')['username'];
                $mutasi -> jenis_transaksi = 'Diamond Free Fire';
                $mutasi -> code = $request->produk;
                $mutasi -> phone = $request->no_pelanggan;
                $mutasi -> status = $GetPrabayarHistory['status'];
                $mutasi -> trxid_api = $trxid_api;
                $mutasi -> note = $GetPrabayarHistory['message'];
                $mutasi->save();
                
                return redirect('/cs/transaksi/vgff/2');
            }
        }else{
            return redirect('/cs/transaksi/vgff/2');
        }
    }
}
