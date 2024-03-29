<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserProfileUpdate extends FormRequest
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
        $user = (!empty($this->id)) ? User::find(decrypt($this->id)) : Auth::user();
        $rule = [
          //  'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'phone' => ['required','numeric',
                Rule::unique('users')->ignore($user->id, 'id')
            ]
        ];
        if (Auth::user()->role == USER_ROLE_USER) {
            $rule['country'] = ['required'];
            $rule['gender'] = ['required'];
        }

        return $rule;
    }

    public function messages()
    {
        return  [
            'first_name' => __('First name can not be empty'),
            'phone.required' => __('Phone number can not be empty'),
            'country.required' => __('Country can not be empty'),
            'phone.numeric' => __('Please enter a valid phone number'),
            'last_name' => __('Last name can not be empty'),
            'gender' => __('Gender can not be empty')
            ];
    }
}
