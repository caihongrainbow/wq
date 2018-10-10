<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;

class Phone extends Authenticatable implements JWTSubject
{
    use Notifiable;

	protected $fillable = ['phone_number', 'area_code'];

	public $timestamps = false;

    public function users(){
    	return $this->belongsToMany(User::class, 'user_auths', 'phone_id', 'user_id')->withTimestamps();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [self::class];
    }
}
