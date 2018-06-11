<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WechatMp extends Model
{
	protected $fillable = ['appid', 'appsecret']; 

	public function users(){
		return $this->belongsToMany(User::class, 'user_wechat_mps');
	}
}
