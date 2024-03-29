<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutations extends Model
{
    use HasFactory;
    public function Customers()
    {
        return $this->belongsTo('App\Models\Customers', 'username');
    }
}
