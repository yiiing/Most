<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Cache;
use PFinal\Passport\Service\TokenService;
use Exception;

class ManageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        //todo...

        $token = '';
        if ($request->headers->get('token')) {
            $token = $request->headers->get('token');
        } else if ($request->get('token')) {
            $token = $request->get('token');
        }

        //登陆验证并生成token
        $tokenService = new TokenService(new \App\Service\ManageStore());
        $tokenService::$tokenType='store';
        /* 查询客户端 */
        try {

            $client = $tokenService->tokenVerify($token, 'center');

        } catch (Exception $e) {
            // pass
            return response()->json(['status' => false, 'data' => '', 'code' => 2001, 'msg' => 'invalid token']);
        }


        $expiresAt = Carbon::now()->addDay(30);
        Cache::put('manageUser', $client, $expiresAt);

        return $next($request);
    }
}
