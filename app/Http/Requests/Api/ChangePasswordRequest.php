<?php
namespace App\Http\Requests\Api;

class ChangePasswordRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'old_password' => ['required', 'min:6', 'max:60'],
            'new_password' => ['required', 'min:6', 'max:60', 'dumbpwd', 'confirmed'],
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
