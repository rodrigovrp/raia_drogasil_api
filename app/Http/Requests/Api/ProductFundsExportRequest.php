<?php
namespace App\Http\Requests\Api;

class ProductFundsExportRequest extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			'year'    => ['required', 'integer', 'in:'.date('Y').','.date('Y', strtotime('+1 year'))],
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
