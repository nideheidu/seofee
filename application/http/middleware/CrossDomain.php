<?php

namespace app\http\middleware;

use think\Response;

class CrossDomain
{
    public function handle($request, \Closure $next)
    {
        header('Access-Control-Allow-Origin: *');
        if (strtoupper($request->method()) == 'OPTIONS') {
            Response::create()->send();

        }
        return $next($request);

    }
}