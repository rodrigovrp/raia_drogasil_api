<?php
namespace App\Http\Requests\Api;

class ProductFundsRequest extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			'product_id'    => ['required', 'integer'],
			'year'    => ['required', 'integer', 'in:'.date('Y').','.date('Y', strtotime('+1 year'))],
			'amount'    => ['required', 'gt:0'],
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
