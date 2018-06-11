<?php

namespace App\Listeners;

use App\Events\WechatOfficialEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WechatOfficialListener
{
    use \App\Models\Traits\AuthAccessHelper;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  WechatOfficialEvent  $event
     * @return void
     */
    public function handle(WechatOfficialEvent $event)
    {

        if($event->isNewSession){
            $user = $this->action('register')->facade('wechat', $event->user, $event->wechatid, $event->insid);
            $sessionKey = config('session.login_key');
            // dd($user);
            session([$sessionKey => $user]);
        }
        // dd($event);
    }
}
