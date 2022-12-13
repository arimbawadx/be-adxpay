<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;
    public function Hutang()
    {
        return $this->hasMany('App\Models\Hutang');
    }
    
    public function Mutations()
    {
        return $this->hasMany('App\Models\Mutations');
    }
}
