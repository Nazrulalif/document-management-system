<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TooManyRequestsHttpException) {
            if ($request->expectsJson()) {
                return response()->json(
                    [
                        'throttle_error' => true,
                        'error' => 'You are doing that too often. Please slow down.',
                    ],
                    429,
                );
            }

            return back()->with('error', 'You are doing that too often. Please slow down.');
        }

        return parent::render($request, $exception);
    }
}
