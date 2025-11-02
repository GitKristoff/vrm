<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        // Symfony Mailer transport exception (Laravel 9+/12+)
        if (interface_exists(\Symfony\Component\Mailer\Exception\TransportExceptionInterface::class)
            && $exception instanceof \Symfony\Component\Mailer\Exception\TransportExceptionInterface) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Mail transport unavailable. Check your internet connection.'], 503);
            }
            return response()->view('errors.mail-offline', [], 503);
        }

        // Legacy SwiftMailer transport exception fallback
        if (class_exists('Swift_TransportException') && is_a($exception, 'Swift_TransportException')) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Mail transport unavailable. Check your internet connection.'], 503);
            }
            return response()->view('errors.mail-offline', [], 503);
        }

        return parent::render($request, $exception);
    }
}
