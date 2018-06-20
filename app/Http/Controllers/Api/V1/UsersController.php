<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Requests\Api\UserRequest;
use App\Http\Requests\Api\InstitutionUserRequest;
use App\Http\Requests\Api\VerificationCodeRequest;

use App\Http\Controllers\Api\Controller;
use App\Transformers\UserTransformer;
use App\Transformers\InstitutionUserTransformer;
use App\Models\Image;
use Auth;

class UsersController extends Controller
{
    use \App\Traits\OAuthorizationHelper;

	public function store(UserRequest $request)
    {
        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return $this->response->item($user, new UserTransformer())
        ->setMeta([
            'access_token' => \Auth::guard('api')->fromUser($user),
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ])
        ->setStatusCode(201);
    }

    public function me(Request $request)
    {
        $user = Auth::guard('api')->user();
        if(is_null($user)){
            return $this->response->error('Token码与获取的实体类型冲突', 403);
        }
        return $this->response->item($user, new InstitutionUserTransformer());
    }

    public function update(UserRequest $request)
    {
        $user = $this->user();

        $attributes = $request->only(['name', 'email', 'introduction']);

        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);

            $attributes['avatar'] = $image->path;
        }
        $user->update($attributes);

        return $this->response->item($user, new InstitutionUserTransformer());
    }

}
