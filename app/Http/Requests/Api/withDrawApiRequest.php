<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class withDrawApiRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rule = [
            'address' => ['required', 'string'],
            'amount' => ['required','numeric'],
            'code' => ['required'],
            'wallet_id' => ['required'],
        ];

        if (!empty($this->message)) {
            $rule['message'] = 'string';
        }

        return $rule;
    }

    public function messages()
    {
        $msg = [
            'address.required' => __('Address is required'),
            'address.string' => __('Address must be a string!'),
            'amount.required' => __('Coin type is required'),
            'amount.numeric' => __('Amount must be numeric field!'),
            'code.required' => __('Code is required'),
            'wallet_id.required' => __('Wallet is required'),
        ];
        if (!empty($this->message)) {
            $msg['message.string'] = __('Message must be a string');
        }

        return $msg;
    }

    protected function failedValidation(Validator $validator)
    {

        $errors = [];
        if ($validator->fails()) {
            $e = $validator->errors()->all();
            foreach ($e as $error) {
                $errors[] = $error;
            }
        }
        $json = ['success'=>false,
            'data'=>[],
            'message' => $errors[0],
        ];
        $response = new JsonResponse($json, 200);

        throw (new ValidationException($validator, $response))->errorBag($this->errorBag)->redirectTo($this->getRedirectUrl());


    }
}
