<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedUrls = [
            'http://127.0.0.1:8000/',
            'https://adainvest.eu',
        ];

        $allowedUrl = '';
        $origin = request()->headers->get('origin');
        $origin = empty($origin) ? 'https://' . request()->headers->get('host') : $origin;
        $originParam = explode('.', $origin)[0];

        if ( in_array($originParam, $allowedUrls) ) {
            $allowedUrl = $origin;
        }

        $request->headers->set('Access-Control-Allow-Origin', $allowedUrl);
        $request->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
        $request->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Request-With');

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin' , $allowedUrl);
        $response->headers->set('Access-Control-Allow-Methods' , 'GET, POST, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Headers' , 'Origin, Content-Type, Accept, Authorization, X-Request-With');

        $response->headers->set('x-content-type-options' , 'nosniff');
        $response->headers->set('x-frame-options' , 'SAMEORIGIN');
        $response->headers->set('x-xss-protection' , '1; mode=block');
        $response->headers->set('strict-transport-security' , 'max-age=31536000; includeSubDomains');
        $response->headers->set('referrer-policy' , 'strict-origin-when-cross-origin');

        return $response;
    }
}
