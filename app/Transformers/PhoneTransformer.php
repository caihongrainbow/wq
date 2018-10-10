<?php

namespace App\Transformers;

use App\Models\Phone;
use League\Fractal\TransformerAbstract;

class PhoneTransformer extends TransformerAbstract
{
    public function transform(Phone $phone)
    {
        return [
            'phone_number' => $phone->phone_number,
            'area_code' => $phone->area_code,
        ];
    }
}