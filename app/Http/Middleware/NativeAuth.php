<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Interfaces\AuthServiceInterface;

class NativeAuth
{
    private $authServiceInterface;

    public function __construct(AuthServiceInterface $authServiceInterface){
        $this->authServiceInterface = $authServiceInterface;
    }
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
        $validate = $this->authServiceInterface->authMiddleware([
            "token" => $request->header('authtoken'),
            "userId" => $request->header('userid'),
            "walletId" => $request->header('walletid'),
        ]);
        if($validate['type'] == "Error"){
            return response()->json([
                'type' => 'Error',
                'msg' => 'Authorization Denied',
                'data' => null
            ], 200);
        }
        
        // inject some global data in request object so that we can use in the future
        $request->current_balance = $validate['data']['user']->current_balance;
        $request->currency = $validate['data']['user']->currency;
        return $next($request);




    }
}
