<?php

namespace App\Transformers;

use App\Models\Institution;
use League\Fractal\TransformerAbstract;

class InstitutionTransformer extends TransformerAbstract
{
    public function transform(Institution $ins)
    {
        return [
            'name' => $ins->name,
            'created_at' => $ins->created_at->toDateTimeString(),
            'updated_at' => $ins->updated_at->toDateTimeString(),
        ];
    }
}