<?php

namespace taitai42\Stklog\Middleware;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use taitai42\Stklog\Model\StackRepository;

/**
 * Created by PhpStorm.
 * User: yannis
 * Date: 5/11/17
 * Time: 3:40 PM
 */
class StklogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        StackRepository::stack('main', null, $request->headers->all());
        Log::info('Request', $request->all());

        return $next($request);
    }

    public function terminate($request, $response)
    {
        Log::info('Response', ['response' => $response]);
    }
}
