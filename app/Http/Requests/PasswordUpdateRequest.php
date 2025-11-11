<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                // Custom rule to check if the provided password matches the DB
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, $this->user()->password)) {
                        $fail('The provided password does not match your current password.');
                    }
                },
            ],
            'new_password' => [
                'required',
                'string',
                'confirmed', // This checks for 'new_password_confirmation'
                Password::min(8)->mixedCase()->numbers(), // Enforce strong password
            ],
        ];
    }
}