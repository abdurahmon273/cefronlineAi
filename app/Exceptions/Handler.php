<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     */

//    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
//    {
//        if ($request->expectsJson()) {
//            return response()->json(['message' => 'Ruxsatsiz kirish. Iltimos, tizimga kiring.'], 401);
//        }
//
//    }
    public function register(): void
    {
        $this->renderable(function (ThrottleRequestsException $e, $request) {

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Juda ko‘p urinish bo‘ldi. Biroz kutib qayta urinib ko‘ring.'
                ], 429);
            }

            return back()
                ->withInput()
                ->withErrors([
                    'throttle' => 'Juda ko‘p urinish bo‘ldi. 1 minutdan keyin qayta urinib ko‘ring.'
                ]);
        });
    }
}
