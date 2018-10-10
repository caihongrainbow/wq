<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitutionProfileSetting extends Model
{
	public $timestamps = false;
	
	protected $fillable = ['field_id', 'type', 'field_key', 'field_name', 'field_type', 'visiable', 'editable', 'required', 'privacy', 'form_type', 'form_default_value', 'validation', 'tips', 'ins_id'];

    public function institutions(){
    	return $this->belongsToMany(Institution::class, 'institution_profiles', 'field_id', 'ins_id', 'field_id', 'id')->withPivot(['field_data']);
    }
}
