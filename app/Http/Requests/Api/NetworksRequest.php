<?php
namespace App\Http\Requests\Api;

class NetworksRequest extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			'name'    => ['required', 'string'],
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
