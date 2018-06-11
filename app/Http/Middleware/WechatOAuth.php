<?php

namespace App\Http\Middleware;

use EasyWeChat\Factory;
use Closure;
use Event;
use http\Env\Request;
use App\Events\WechatOfficialEvent;

use App\Models\Institution;

class WechatOAuth
{
    use \App\Models\Traits\AuthAccessHelper;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //是否是已登录的Session
        $isNewSession = false;

        $ins = Institution::where('orgid', $request->orgid)->first();
        $wechatmps = $ins->wechatMps()->first();

        $config = ['app_id' => $wechatmps->appid, 'secret' => $wechatmps->appsecret];
        $officialAccount = Factory::officialAccount($config);
        $scopes = ['snsapi_userinfo'];
        
        if(!isLogin()){
            if ($request->has('code')) {
                $isNewSession = true;

                $user = $this->action('register')->facade('wechat', $officialAccount->oauth->user(), $wechatmps->id, $ins->id);
                
                $sessionKey = config('session.login_key');
            
                session([$sessionKey => $user ?? []]);
                // Event::fire(new WechatOfficialEvent($user, $isNewSession, $ins->id, $wechatmps->id));
                
                return redirect()->to($this->getTargetUrl($request));
            } 
            return $officialAccount->oauth->scopes($scopes)->redirect($request->fullUrl());
        }
        return $next($request);
    }

    protected function getTargetUrl($request)
    {
        $queries = array_except($request->query(), ['code', 'state']);

        return $request->url().(empty($queries) ? '' : '?'.http_build_query($queries));
    }
}
