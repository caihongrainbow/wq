<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitutionUserCredit extends Model
{
    protected $fillable = ['custom_id', 'history_credit', 'current_credit', 'role_level_id'];

    public function custom(){
    	return $this->belongsTo(InstitutionUser::class, 'custom_id', 'id');
    }
}

