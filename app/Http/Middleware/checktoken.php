<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use GuzzleHttp\Client;
use Closure;

class checktoken
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
        // $baseUrl = env('ACCOUNTS_URL');
        $baseUrl = "https://accounts.ibial.com";
        
        $getToken = $request->header();
        
        // catch if token is avaialble
        if(!isset($getToken['authorization'][0])){
            return response()->json([
                'message' => "Token Missing",
                'status' => '401',
            ], 401);
        }
        $token = $getToken['authorization'][0];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl."/api/v1/getUser");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type:application/json",
            'Access-Control-Allow-Origin: *',
            "Accept:application/json",
            'Authorization: '.$token,
        ));

        $output = curl_exec($ch);
        curl_close($ch);

        $output = json_decode($output);

        // catch if error
        // base on getUser on Accounts MS
        if(isset($output->message)){
            return response()->json([
                'message' => $output->message,
                'status' => '401',
            ], 401);
        }

        // when token is verified
        $request->request->add(['user_id' => $output->success->id]);
        return $next($request);
        
    }
}
