<?php
namespace App\Http\Requests\Api;

class LoginRequest extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			'login'    => ['required', 'string'],
			'password' => ['required', 'string'],
			'remember_me' => ['boolean'],
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
