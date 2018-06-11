<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class VerificationCodesController extends Controller
{
    public function store()
    {
        return $this->response->array(['test_message' => 'store verification code v2']);
    }
}
