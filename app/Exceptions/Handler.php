<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Exception $e) {
            if ($e instanceof BadRequestException) {
                return response([
                    'error' => $e->getMessage(),
                ], 400);
            } else if ($e instanceof NotFoundHttpException) {
                return response([
                    'error' => 'resource_not_found',
                ], 404);
            } else if ($e instanceof ValidationException) {
                return response([
                    'error' => $e->errors(),
                ], 422);
            } else {
                return response([
                    'error' => 'internal_server_error',
                ], 500);
            }
        });
    }
}
