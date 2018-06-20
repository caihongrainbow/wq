<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
	protected $fillable = ['phone_number', 'area_code'];

	public $timestamps = false;

    public function users(){
    	return $this->belongsToMany(User::class, 'institution_user_auths', 'phone_id', 'user_id')->withTimestamps();
    }
}
