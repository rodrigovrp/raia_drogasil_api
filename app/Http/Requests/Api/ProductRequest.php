<?php
namespace App\Http\Requests\Api;

class ProductRequest extends Request
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
			'category_id'    => ['required', 'integer'],
			'brand_id'    => ['required', 'integer'],
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
