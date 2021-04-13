<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

class DomainCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param $allowed
     * @return mixed
     */
    public function handle($request, Closure $next, ... $allowedHosts)
    {
        $requestHost = $request->getClientIp();

        \Log::info('received host: ' . $requestHost . ' allowed hosts: ' . implode(",", $allowedHosts));

        if (!app()->runningUnitTests()) {
            if (!\in_array($requestHost, $allowedHosts, false)) {
                $requestInfo = [
                    'host' => $requestHost,
                    'ip' => $request->getClientIp(),
                    'url' => $request->getRequestUri(),
                    'agent' => $request->header('User-Agent'),
                ];

                throw new SuspiciousOperationException('This host is not allowed');
            }
        }

        return $next($request);
    }
}
