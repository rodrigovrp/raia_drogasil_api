<?php
namespace App\Http\Requests\Api;

class ActionTypesRequest extends Request
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
			'description'    => ['string'],
			'status' => ['required', 'in:1,0'],
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
