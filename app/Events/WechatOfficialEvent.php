<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use Overtrue\Socialite\User;

class WechatOfficialEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $isNewSession;

    public $insid;

    public $wechatid;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $isNewSession = false, int $insid = 0, $wechatid = 0)
    {
        $this->user = $user;
        $this->isNewSession = $isNewSession;
        $this->insid = $insid;
        $this->wechatid = $wechatid;
    }

    public function getUser(){
        return $this->user; 
    }

    public function isNewSession(){
        return $this->isNewSession;
    }

    public function getInsid(){
        return $this->insid;
    }

    public function getWechatid(){
        return $this->getWechatid();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
