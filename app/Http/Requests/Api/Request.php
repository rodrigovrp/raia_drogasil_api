<?php
namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

abstract class Request extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Extend the default getValidatorInstance method
	 * so fields can be modified or added before validation
	 *
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function getValidatorInstance()
	{
        $input = $this->all();
        request()->merge($input); // Required!
        $this->merge($input);

		return parent::getValidatorInstance();
	}

	/**
	 * Handle a failed validation attempt.
	 *
	 * @param Validator $validator
	 * @throws ValidationException
	 */
	protected function failedValidation(Validator $validator)
	{
		if ($this->ajax() || $this->wantsJson()) {
			// Get Errors
			$errors = (new ValidationException($validator))->errors();

			// Get Json
			$json = [
				'message' => __('validation.error_occured'),
				'errors'    => $errors,
			];

			throw new HttpResponseException(response()->json($json, JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
		}

		parent::failedValidation($validator);
	}


}
