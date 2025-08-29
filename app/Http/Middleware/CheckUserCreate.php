<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CheckUserCreate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         if(Session::get('role')=='Super Admin'){
            return $next($request);
        }else{
            $userID = Session::get('userID');
            $access = DB::table('lms_users_details')
                    ->select('createUserCheck')
                    ->where('userID', $userID)
                    ->first();
               
            if($access->createUserCheck != '1') {
                 abort(404);
            }
            return $next($request); 
        }
    }
}
