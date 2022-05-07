<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class APICustomers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // membawa parameter username
        $customer = \App\Models\Customers::where('username', $request->username)->where('deleted', 0)->first();
        $cs = \App\Models\CustomerServices::where('username', $request->username)->where('deleted', 0)->first();

        if ($customer) {
            return $next($request);
        } else {
            return response()->json([
                'message' => 'Tidak ada akses!'
            ], 200);
        }
    }
}
