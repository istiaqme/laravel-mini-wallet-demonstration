<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use App\Exceptions\ServiceException;
use App\Exceptions\AuthException;
use App\Exceptions\TransactionException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        // Exceptions from Service Methods
        $this->renderable(function (ServiceException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'type' => "Error",
                    'msg' => $e->getMessage(),
                    'data' => null
                ], 400);
            }
        });

        // Exceptions from QueryException
        $this->renderable(function (QueryException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'type' => "Error",
                    'msg' => "Database Error. System Is Down For Sometimes.",
                    'data' => null
                ], 500);
            }
        });


        // Exceptions from ModelNotFound
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'type' => "Error",
                    'msg' => "Database Error. -M",
                    'data' => null
                ], 500);
            }
        });


        // Exceptions from AuthException
        $this->renderable(function (AuthException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'type' => "Error",
                    'msg' => "Authorization Denied.",
                    'data' => null
                ], 401);
            }
        });


        // Exceptions from TransactionException
        $this->renderable(function (TransactionException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'type' => "Error",
                    'msg' => $e->getMessage(),
                    'data' => null
                ], 400);
            }
        });





    }
}
