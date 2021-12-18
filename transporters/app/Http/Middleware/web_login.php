<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;
use Illuminate\Support\Facades\Session;
class web_login
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
        //return $next($request);
        if(Session::has('web_user_id') && Session::has('web_user_name') && Session::has('web_user_email'))
        {
            if(Session::get('is_user_verify') == 1) {
                return $next($request);
            } else {
                return redirect('/verification');
            }
        } elseif(Cookie::get('web_user_post_id') !== null && 
        Cookie::get('web_user_id') !== null && 
        Cookie::get('web_user_name') !== null &&
        Cookie::get('web_user_email') !== null &&
        Cookie::get('is_user_verify') !== null) {
            Session::put('web_user_post_id', Cookie::get('web_user_post_id'));
            Session::put('web_user_id', Cookie::get('web_user_id'));
            Session::put('web_user_name', Cookie::get('web_user_name'));
            Session::put('web_user_email', Cookie::get('web_user_email'));
            Session::put('is_user_verify', Cookie::get('is_user_verify'));

            if(Session::get('is_user_verify') == 1) {
                return $next($request);
            } else {
                return redirect('/verification');
            }
            
        } else{
             return redirect('signin');
        }
    }
}
