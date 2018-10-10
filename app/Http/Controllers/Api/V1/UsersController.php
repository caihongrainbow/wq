<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Requests\Api\UserRequest;
use App\Http\Requests\Api\InstitutionUserRequest;
use App\Http\Requests\Api\VerificationCodeRequest;

use App\Http\Controllers\Api\Controller;
use App\Transformers\UserTransformer;
use App\Models\Image;
use Auth;

class UsersController extends Controller
{
    use \App\Traits\OAuthorizationHelper;

    protected $guard = 'user';

	public function store(Request $request)
    {
        $phoneNumber = $request->phone_number; 
        $areaCode = $request->area_code ?? '86';
        $name = $request->name;
        $realname = $request->realname;

        //权限验证
        $user = $this->user();

        if(is_null($user)){
            return $this->response->errorForbidden('Token码与获取的实体类型冲突');
        }

        if(!$user->can('add_custom')){
            return $this->response->errorForbidden('您无法享有此权限'); 
        }

        $this->institution($user->orgid)->action('login')->facade('mobile', $phoneNumber, $areaCode, ['name' => $name, 'realname' => $realname]);

        return $this->response->created();
    }

    public function currentShow(Request $request)
    {
        $user = $this->user();
        return $this->response->item($user, new UserTransformer());
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
