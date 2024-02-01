<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable; // Make sure to import Throwable

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $e)
    {
        parent::report($e);
    }

    public function render($request, Throwable $e)
    {
        if ($request->wantsJson()) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    private function handleApiException(Request $request, Throwable $e): Response
    {
        $exception = $this->prepareException($e);

        if ($exception instanceof \Illuminate\Http\Exceptions\HttpResponseException) {
            $exception = $exception->getResponse();
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            $exception = $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
        }

        return $this->customApiResponse($exception);
    }

    private function customApiResponse($exception): Response
    {
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        $response = [
            'status' => $statusCode,
            'error' => '',
            'code' => 0,
            'message' => '',
        ];

        switch ($statusCode) {
            case 401:
                $response['message'] = 'Unauthorized';
                break;
            case 403:
                $response['message'] = 'Forbidden';
                break;
            case 404:
                $response['message'] = 'Not Found';
                break;
            case 405:
                $response['message'] = 'Method Not Allowed';
                break;
            case 422:
                $response['message'] = $exception->original['message'] ?? 'Unprocessable Entity';
                $response['errors'] = $exception->original['errors'] ?? [];
                break;
            default:
                $response['message'] = ($statusCode == 500) ? 'Whoops, looks like something went wrong' : $exception->getMessage();
                break;
        }

        if (config('app.debug')) {
            $response['errorMessage'] = $exception->getMessage();
            $response['code'] = $exception->getCode();
        }

        return response()->json([
            'status' => $response['status'],
            'error' => $response['errorMessage'],
            'code' => $response['code'],
            'message' => $response['message']
        ], $statusCode);
    }
}
