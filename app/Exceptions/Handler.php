<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception instanceof InvalidSignatureException) {
            return response()->json([
                'data' => [
                    'result' => 'error',
                    'message' => 'Your signing request is invalid, please try again.',
                    'error' => $this->getDetailedError($exception)
                ]
            ], 403);
        }

        if($exception instanceof AuthenticationException ){
            if (strpos(request()->path(), "dashboard") !== false) {
                return redirect('dashboard/login');
            }
            return response()->json([
                'data' => [
                    'result' => 'error',
                    'message' => 'You are not currently authenticated.',
                    'error' => $this->getDetailedError($exception)
                ]
            ], 401);

        }

        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'data' => [
                    'result' => 'error',
                    'message' => 'You do not have sufficient privileges to carry out this action.',
                    'error' => $this->getDetailedError($exception)
                ]
            ], 403);
        }

        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'data' => [
                    'result' => 'error',
                    'message' => 'Resource not found for provided ID.',
                    'error' => $this->getDetailedError($exception)
                ]
            ], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'data' => [
                    'result' => 'error',
                    'message' => $request->method(). ' is not allowed on this endpoint',
                    'error' => $this->getDetailedError($exception)
                ]
            ], 404);
        }

        if ($exception instanceof ValidationException || $exception instanceof ApiException) {
            return response()->json([
                'data' => [
                    'result' => 'error',
                    'message' => $exception->getMessage(),
                    'errors' => isset($exception->validator) ? $exception->validator->getMessageBag() : []
                ]
            ], 422);
        }

        Log::info($exception);
        return response()->json([
            'data' => [
                'result' => 'error',
                'message' => 'Unhandled exception error.',
                'error' => $this->getDetailedError($exception)
            ]
        ], 500);

    }

    /**
     * @param \Exception $exception
     * @return string
     */
    protected function getDetailedError($exception)
    {
        if ($exception->getMessage()) {
            return class_basename($exception). ': '. $exception->getMessage();
        }
        return class_basename($exception);
    }

}
