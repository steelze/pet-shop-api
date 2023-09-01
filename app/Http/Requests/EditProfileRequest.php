<?php

namespace App\Http\Requests;

use App\Models\User;
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
        $user = $this->uuid
            ? User::where('uuid', $this->uuid)->firstOrFail()
            : Auth::user();

        return [
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'required|confirmed',
            'avatar' => 'nullable|uuid',
            'address' => 'required',
            'phone_number' => 'required|numeric',
            'is_marketing' => 'nullable',
        ];
    }
}
