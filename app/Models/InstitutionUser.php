<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class InstitutionUser extends Authenticatable implements JWTSubject
{
	use SoftDeletes;

	protected $table = 'institution_users';

	protected $dates = ['deleted_at'];

	protected $fillable = ['user_id', 'ins_id', 'auth_id', 'is_admin', 'clientid'];

    public function institution(){
    	return $this->belongsTo(Institution::class, 'ins_id', 'id');
    }

    public function user(){
    	return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
