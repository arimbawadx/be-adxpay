<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Config;


class apiSerpulController extends Controller
{
    public function GetAkunSerpul()
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
        return $saldoUtama;
        $saldo = $saldoUtama['responseData']['balance'];
        // end GET AKUN/CEK SALDO SERPUL
    }

    public function GetPrabayarCategory()
    {
        // Get Prabayar Category
        $curlGetPrabayarCategory = curl_init();
        curl_setopt_array($curlGetPrabayarCategory, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/category",
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
        $responseGetPrabayarCategory = curl_exec($curlGetPrabayarCategory);
        $decodeResponseGetPrabayarCategory=json_decode($responseGetPrabayarCategory, true);
        $GetPrabayarCategory = $decodeResponseGetPrabayarCategory;
        return $GetPrabayarCategory;
        // $PrabayarCategory = $GetPrabayarCategory['responseData']['balance'];
        // end Get Prabayar Category
    }

    public function GetPrabayarOperator()
    {
        // Get Prabayar Operator
        $curlGetPrabayarOperator = curl_init();
        curl_setopt_array($curlGetPrabayarOperator, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/operator?product_id=WIFI",
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
        $decodeResponseGetPrabayarOperator=json_decode($responseGetPrabayarOperator, true);
        $GetPrabayarOperator = $decodeResponseGetPrabayarOperator;
        return $GetPrabayarOperator;
        // $PrabayarOperator = $GetPrabayarOperator['responseData']['balance'];
        // end Get Prabayar Operator


        // example prefix
        // $phone=$request->no_hp;
        // $phone4=substr(trim($phone), 0, 4); //memotong no hp 4 awal
        // foreach ($GetPrabayarOperator as $po) {
        //     $prefix = explode(',', $po->prefix);
        //     if (in_array($phone4, $prefix)) {
        //         $idProduk = $po->product_id;
        //     }
        // }
        // end example prefix
    }

    public function GetPrabayarProduct()
    {
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/product?product_id=PIUIST",
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
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct, true);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct['responseData'];
        return $GetPrabayarProduct;
        // $PrabayarProduct = $GetPrabayarProduct['responseData']['balance'];
        // end Get Prabayar Product
    }

    public function PostPrabayar()
    {
        // Post Prabayar
        $dataPostPrabayar = [
            "destination"=>"011401021881505",
            "product_id"=>"CEKBRI",
            "ref_id"=>date('dmYhis')
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
        return $decodeResponsePostPrabayar;
        // end Post Prabayar
    }

    public function GetPrabayarHistory()
    {
        // Get Prabayar History
        $curlGetPrabayarHistory = curl_init();
        curl_setopt_array($curlGetPrabayarHistory, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/20211211064235",
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
        return $GetPrabayarHistory = $decodeResponseGetPrabayarHistory;
        // end Get Prabayar History
    }



    // pascabayar
    public function GetPascabayarCategory()
    {
        // Get Prabayar Category
        $curlGetPrabayarCategory = curl_init();
        curl_setopt_array($curlGetPrabayarCategory, array(
            CURLOPT_URL => "https://api.serpul.co.id/pascabayar/category",
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
        $responseGetPrabayarCategory = curl_exec($curlGetPrabayarCategory);
        $decodeResponseGetPrabayarCategory=json_decode($responseGetPrabayarCategory, true);
        $GetPrabayarCategory = $decodeResponseGetPrabayarCategory;
        return $GetPrabayarCategory;
        // $PrabayarCategory = $GetPrabayarCategory['responseData']['balance'];
        // end Get Prabayar Category
    }

    public function GetPascabayarProduct()
    {
        // Get Prabayar Product
        $curlGetPrabayarProduct = curl_init();
        curl_setopt_array($curlGetPrabayarProduct, array(
            CURLOPT_URL => "https://api.serpul.co.id/pascabayar/product?product_id=PDAM",
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
        $decodeResponseGetPrabayarProduct=json_decode($responseGetPrabayarProduct, true);
        $GetPrabayarProduct = $decodeResponseGetPrabayarProduct;
        return $GetPrabayarProduct;
        // $PrabayarProduct = $GetPrabayarProduct['responseData']['balance'];
        // end Get Prabayar Product
    }

    public function PostPascabayar()
    {
        // Post Prabayar
        $dataPostPrabayar = [
            "destination"=>"011401021881505",
            "product_id"=>"CEKBRI",
            "ref_id"=>date('dmYhis')
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
        return $decodeResponsePostPrabayar;
        // end Post Prabayar
    }

    public function GetPascabayarHistory()
    {
        // Get Prabayar History
        $curlGetPrabayarHistory = curl_init();
        curl_setopt_array($curlGetPrabayarHistory, array(
            CURLOPT_URL => "https://api.serpul.co.id/prabayar/history/20211212463136494",
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
        return $GetPrabayarHistory = $decodeResponseGetPrabayarHistory;
        // end Get Prabayar History
    }
}
