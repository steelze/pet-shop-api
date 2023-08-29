<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EditProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => [
                'required',
                'email',
                ($this->user)
                    ? Rule::unique('users')->ignore($this->user->id)
                    : Rule::unique('users')->ignore(Auth::user()->id),
            ],
            'password' => 'required|confirmed',
            'avatar' => 'nullable|uuid',
            'address' => 'required',
            'phone_number' => 'required|numeric',
            'is_marketing' => 'nullable',
        ];
    }
}
