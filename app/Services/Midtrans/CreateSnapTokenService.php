<?php

namespace App\Services\Midtrans;

use Midtrans\Snap;

class CreateSnapTokenService extends Midtrans
{
    protected $transaksi;
    
    public function __construct($transaksi)
    {
        parent::__construct();
        
        $this->transaksi = $transaksi;
    }
    
    public function getSnapToken()
    {
        $params = [
            'transaction_details' => [
                'order_id' => $this->transaksi->number,
                'gross_amount' => $this->transaksi->total_price,
            ],
            'customer_details' => [
                'first_name' => 'Martin Mulyo Syahidin',
            ]
        ];
        
        $snapToken = Snap::getSnapToken($params);
        
        return $snapToken;
    }
}