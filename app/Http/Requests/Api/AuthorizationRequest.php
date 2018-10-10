<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class AuthorizationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $name = $this->route()->getAction();

        switch ($name['as']) {
            case 'api.authorizations.store':
                return [
                    'username' => 'required|string',
                    'password' => 'required|string',
                ];
                break;
            case 'api.authorizations.mobile.store':
                return [
                    'phone_number' => ['required','regex:/^[1][3-8]\d{9}$|^([6|9])\d{7}$|^[0][9]\d{8}$|^[6]([8|6])\d{6}$/','string'],
                    'area_code' => 'required|string|in:86,852,853,886',
                    'verification_code' => 'required|string',
                ];
                break;
            case 'api.institutions.authorizations.store':
                return [
                    'username' => 'required|string',
                    'password' => 'required|string',
                    'orgid' => 'required|string',
                ];
            case 'api.institutions.authorizations.wechat.store':
                return [
                    'orgid' => 'required|string',
                ];
            case 'api.institutions.authorizations.mobile.store':
                return [
                    'phone_number' => ['required','regex:/^[1][3-8]\d{9}$|^([6|9])\d{7}$|^[0][9]\d{8}$|^[6]([8|6])\d{6}$/','string'],
                    'area_code' => 'required|string|in:86,852,853,886',
                    'verification_code' => 'required|string',
                    'orgid' => 'required|string',
                ];
            default:
                return [];
        }
    }

    public function attributes(){
        return [
            'username' => '用户名',
            'password' => '密码',
        ];
    }

    public function messages(){
        return [
            'username.required' => '缺少必要的参数username',
            'password.required' => '缺少必要的参数password',
            'orgid.required' => '缺少必要的参数orgid',
            'phone_number.required' => '缺少必要的参数phone_number',
            'area_code.required' => '缺少必要的参数area_code',
            'verification_code.required' => '缺少必要的参数verification_code',
        ];
    }
}