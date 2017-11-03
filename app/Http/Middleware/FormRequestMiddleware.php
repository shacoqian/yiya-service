<?php
/**
 * Created by PhpStorm.
 * User: qianfeng
 * Date: 17-5-5
 * Time: 上午09:11
 */


namespace App\Http\Middleware;

use Closure;

class FormRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $source)
    {
        $className = 'App\\Http\\Requests\\' . $source;
        $formRequest = new $className();
        if(!$formRequest->handle($request)){
            return response()->json([
                'status' => 0,
                'result' => [],
                'message'=> $formRequest->getFirstErrorMessage(),
                'errors' => $formRequest->getErrorMessages()
            ]);
        }
        return $next($request);
    }
}
