<?php

namespace App\Http\Controllers;

use App\Helper\UrlHelper;
use App\Jobs\ProcessAnlysis;
use App\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Agent\Agent;
use MongoDB\Driver\Session;
use Illuminate\Support\Facades\Redis;
use Tymon\JWTAuth\JWTAuth;

class UrlController extends Controller
{
    public function generateUrl(Request $request){
        $this->validate($request, [
            'long_url' => 'required'
        ]);

        $currentUser = Auth::user();
        $new_url = UrlHelper::generateUrl($request->long_url);
        $url =new Url();
        $url->long_url = $request->long_url;
        $url->short_url = $new_url;
        $url->user_id = $currentUser->id;
        $url->save();

        return response()->json(['status' => 0,'url'=>$new_url]);
    }

    public function redirectUrl(Request $request,string $url){

        if(!$url){
            return abort(404);
        }
        //todo add redis
        $long_url = Redis::get($url);
        //if not in redis
        $original=null;

        if($long_url==null){

            $original = Url::where('short_url',$url)->first();
            if(!$original){
                return abort(404);
            }
            $long_url = $original->long_url;
        }
        $agent = new Agent();
        $browser = $agent->browser();

        ProcessAnlysis::dispatch($url,
            $browser,
            UrlHelper::is_mobile($_SERVER['HTTP_USER_AGENT']),
            session()->getId(),
            $original);

        return  Redirect::to( 'http://'.$long_url);

    }
}
