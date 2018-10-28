<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

//表单请求验证（FormRequest） 是 Laravel 框架提供的用户表单数据验证方案，此方案相比手工调用 validator 来说，能处理更为复杂的验证逻辑，更加适用于大型程序。在本课程中，我们将统一使用 表单请求验证来处理表单验证逻辑。
class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 权限控制
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 表单验证
     * @return array
     */
    public function rules()
    {
        return [
            'name'          => 'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name,' . Auth::id(),
            'email'         => 'required|email',
            'introduction'  => 'max:80',
            'avatar'        => 'mimes:jpeg,bmp,png,gif|dimensions:min_width=200,min_height=200',
        ];
    }

    //自定义报错信息
    public function messages()
    {
        return [
            'avatar.mimes'      => '头像必须是 jpeg, bmp, png, gif 格式的图片',
            'avatar.dimensions' => '图片的清晰度不够，宽和高需要 200px 以上',
            'name.unique'       => '用户名已被占用，请重新填写',
            'name.regex'        => '用户名只支持英文、数字、横杠和下划线。',
            'name.between'      => '用户名必须介于 3 - 25 个字符之间。',
            'name.required'     => '用户名不能为空。',
        ];
    }
}
