<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitutionType extends Model
{
    protected $fillable = ['title', 'sign'];

    public function institutions(){
    	return $this->hasMany(Institution::class, 'type_id');
    }
}
