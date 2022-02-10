<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuthToken;

class NativeAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // wildcard check
        $authTokens = AuthToken::where('token', $request->header('authtoken'))->where('user_id', $request->header('userid'))->where('status', 'Active')->get();
        if(count($authTokens) == 1) {
            // user and wallet get validated here
            $users = User::where('id', $request->header('userid'))->where('wallet_id', $request->header('walletid'))->get();
            if(count($users) == 1) {
                $request->current_balance = $users[0]->current_balance;
                $request->currency = $users[0]->currency;
                return $next($request);
            }
            else {
                return response()->json([
                    'type' => 'Error',
                    'msg' => 'Authorization Denied',
                    'data' => null
                ], 200);
            }
            
        }
        else {
            return response()->json([
                'type' => 'Error',
                'msg' => 'Authorization Denied',
                'data' => null
            ], 200);
        }
    }
}
