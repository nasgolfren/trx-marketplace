<?php

namespace App\Http\Middleware;

use App\Models\AccessLog as ModelsAccessLog;
use Closure;
use Illuminate\Http\Request;

class AccessLog
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
        $postValues = null;

        if (!empty($request->post())) {
            $postValues = json_encode($request->post());
        }

        $data = [
            'ip_address' => $request->ip(),
            'url' => $request->url(),
            'https' => $request->secure(),
            'request_type' => $request->method(),
            'get_values' => empty($request->query()) ? null : stripslashes(json_encode($request->query())),
            'post_values' => $postValues,
        ];

        ModelsAccessLog::create($data);

        return $next($request);
    }
}
