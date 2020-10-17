<?php
namespace App\Http\Requests\Api;

class ResetPasswordRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'token' => ['required'],
            'login'    => ['required'],
            'password' => ['required', 'min:6', 'max:60', 'dumbpwd', 'confirmed'],
        ];

        return $rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        $messages = [];

        return $messages;
    }
}
