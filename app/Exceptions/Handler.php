<?php

namespace App\Exceptions;

use Throwable;
use Inertia\Inertia;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {

            // 👉 API bleibt JSON
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Server Error'
                ], $this->getStatusCode($e));
            }

            // 👉 Inertia Requests (inkl. SSR!)
            if ($request->header('X-Inertia')) {

                $status = $this->getStatusCode($e);

                return Inertia::render("Errors/{$status}", [
                    'status' => $status,
                ])->toResponse($request)->setStatusCode($status);
            }

            // 👉 Fallback (z.B. ohne JS, direkte Requests)
            $status = $this->getStatusCode($e);

            return Inertia::render("Errors/{$status}", [
                'status' => $status,
            ])->toResponse($request)->setStatusCode($status);
        });
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'success' => false,
            'message' => __('validation.failed'),
            'errors' => $exception->errors(),
        ], $exception->status);
    }

    private function getStatusCode(Throwable $e): int
    {
        if ($e instanceof HttpExceptionInterface) {
            return $e->getStatusCode();
        }

        return 500;
    }
}   
