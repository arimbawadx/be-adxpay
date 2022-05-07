<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\authBRI;


class briApiCSController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function CekSaldo()
    {
        // get Token
        $curlGetToken = curl_init();
        curl_setopt_array($curlGetToken, array(
            CURLOPT_URL => "https://sandbox.partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "client_id=NauW74AQA7AryrN07F8tqiWxxvksAH3i&client_secret=FcRuqMkVcrtSfVr6",
            CURLOPT_HTTPHEADER => array(
            // Set Here Your Requesred Headers
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));
        $responseGetToken = curl_exec($curlGetToken);
        $decodeResponseToken=json_decode($responseGetToken, false);
        $token = $decodeResponseToken->access_token;
        // end Get Token

        // get Timestamp
        date_default_timezone_set('UTC');
        $timestamp = date('Y-m-d\TH:i:s.Z\Z', time());
        // end get Timestamp

        // save token & timestamp
        $NewAuthBRI = new authBRI;
        $NewAuthBRI->token = $token;
        $NewAuthBRI->timestamp = $timestamp;
        $NewAuthBRI->save();
        // end save token & timestamp

        // get last created token & timestamp
        $lastIDauthBRICreated=authBRI::max('id');
        $authBRILastCreated=authBRI::where('id', $lastIDauthBRICreated)->get()->first();
        $tokenNow = $authBRILastCreated->token;
        $timestampNow = $authBRILastCreated->timestamp;
        // end get last created token & timestamp


        // get Signature
        $payloadInformasiRek = "path=/v2/inquiry/011401021881505&verb=GET&token=Bearer $tokenNow&timestamp=$timestampNow&body=";
        $signatureSecretKey = "FcRuqMkVcrtSfVr6";
        $hashpayloadInformasiRek = hash_hmac('sha256', $payloadInformasiRek, $signatureSecretKey, true );
        $signatureInformasiRek = base64_encode($hashpayloadInformasiRek);
        // End get Signature


        // add signature & description
        $updateAuthBRI = authBRI::where('token', $tokenNow)->get()->first();
        $updateAuthBRI->signature = $signatureInformasiRek;
        $updateAuthBRI->description = "Cek Saldo Rekening";
        $updateAuthBRI->save();
        // end add signature & description


        // get last signature
        $signatureNow = $updateAuthBRI->signature;
        //end get last signature


        // Cek Saldo BRI 011401021881505
        $curlCekSaldoBRI = curl_init();
        curl_setopt_array($curlCekSaldoBRI, array(
            CURLOPT_URL => "https://sandbox.partner.api.bri.co.id/v2/inquiry/011401021881505",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
        // Set Here Your Requesred Headers
                'Authorization: Bearer '.$tokenNow,
                'BRI-SIGNATURE: '.$signatureNow,
                'BRI-TIMESTAMP: '.$timestampNow,
            ),
        ));
        $responseSaldoBRI = curl_exec($curlCekSaldoBRI);
        $decodeResponseSaldoBRI=json_decode($responseSaldoBRI, true);
        $SaldoBRI = $decodeResponseSaldoBRI;
        return $SaldoBRI;
        // end Cek Saldo BRI
    }

    public function RiwayatTransaksi()
    {
        // get Token
        $curlGetToken = curl_init();
        curl_setopt_array($curlGetToken, array(
            CURLOPT_URL => "https://sandbox.partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "client_id=NauW74AQA7AryrN07F8tqiWxxvksAH3i&client_secret=FcRuqMkVcrtSfVr6",
            CURLOPT_HTTPHEADER => array(
            // Set Here Your Requesred Headers
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));
        $responseGetToken = curl_exec($curlGetToken);
        $decodeResponseToken=json_decode($responseGetToken, false);
        $token = $decodeResponseToken->access_token;
        // end Get Token

        // get Timestamp
        date_default_timezone_set('UTC');
        $timestamp = date('Y-m-d\TH:i:s.Z\Z', time());
        // end get Timestamp

        // save token & timestamp
        $NewAuthBRI = new authBRI;
        $NewAuthBRI->token = $token;
        $NewAuthBRI->timestamp = $timestamp;
        $NewAuthBRI->save();
        // end save token & timestamp

        // get last created token & timestamp
        $lastIDauthBRICreated=authBRI::max('id');
        $authBRILastCreated=authBRI::where('id', $lastIDauthBRICreated)->get()->first();
        $tokenNow = $authBRILastCreated->token;
        $timestampNow = $authBRILastCreated->timestamp;
        // end get last created token & timestamp


        // get Signature
        $data = [
            "accountNumber"=>"008301031142500",
            "startDate"=>"2020-12-01",
            "endDate"=>"2020-12-11"
        ];
        $data_encode = json_encode($data);
        // return $data_encode;
        $payloadInformasiRek = "path=/v2.0/statement&verb=POST&token=Bearer $tokenNow&timestamp=$timestampNow&body=$data_encode";
        $signatureSecretKey = "FcRuqMkVcrtSfVr6";
        $hashpayloadInformasiRek = hash_hmac('sha256', $payloadInformasiRek, $signatureSecretKey, true );
        $signatureInformasiRek = base64_encode($hashpayloadInformasiRek);
        // End get Signature


        // add signature & description
        $updateAuthBRI = authBRI::where('token', $tokenNow)->get()->first();
        $updateAuthBRI->signature = $signatureInformasiRek;
        $updateAuthBRI->description = "Cek Riwayat Transaksi Rekening";
        $updateAuthBRI->save();
        // end add signature & description


        // get last signature
        $signatureNow = $updateAuthBRI->signature;
        //end get last signature


        // Cek Riwayat Transakasi
        $curlRiwayatTransaksiBRI = curl_init();
        curl_setopt_array($curlRiwayatTransaksiBRI, array(
            CURLOPT_URL => "https://sandbox.partner.api.bri.co.id/v2.0/statement",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
        // Set Here Your Requesred Headers
                'BRI-Timestamp: '.$timestampNow,
                'BRI-Signature: '.$signatureNow,
                'Content-Type: application/json',
                'BRI-External-Id: '.$lastIDauthBRICreated,
                'Authorization: Bearer '.$tokenNow,
            ),
        ));
        $responseRiwayatTransaksi = curl_exec($curlRiwayatTransaksiBRI);
        $decodeResponseRiwayatTransaksi=json_decode($responseRiwayatTransaksi, true);
        $RiwayatTransaksi = $decodeResponseRiwayatTransaksi;
        return $RiwayatTransaksi;
        //End Cek Riwayat Transakasi

    }
}
