<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Psy\TabCompletion\Matcher\FunctionDefaultParametersMatcher;
use Symfony\Component\HttpFoundation\Response;

class RemoveHeaders
{

    private $removeHeaders = [
        'X-Powered-By',
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @var Response $response
         */

        foreach($this->removeHeaders as $header){
            header_remove('X-Powered-By');
        }

        return $next($request);
    }
}
