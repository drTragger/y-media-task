<?php

namespace App\Exceptions;

use Firebase\JWT\ExpiredException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e): JsonResponse
    {
        return match (true) {
            $e instanceof NotFoundHttpException => response()->json([
                'status' => false,
                'message' => 'Route not found.'
            ], Response::HTTP_NOT_FOUND),
            $e instanceof ValidationException => response()->json([
                'status' => false,
                'message' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY),
            $e instanceof ModelNotFoundException => response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND),
            $e instanceof AuthException => response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST),
            $e instanceof ExpiredException => response()->json([
                'status' => false,
                'message' => 'Token is expired.'
            ], Response::HTTP_UNAUTHORIZED),
            default => response()->json([
                'status' => false,
                'message' => config('app.debug') ? $e->getMessage() . ' in file ' . $e->getFile() . ' : ' . $e->getLine() : 'Server error.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }
}
