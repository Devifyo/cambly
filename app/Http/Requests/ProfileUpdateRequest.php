<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Anyone authenticated can try, policy/controller handles specifics
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'gender' => 'required',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // This rule checks if the email is unique, BUT ignores the current user's ID
                Rule::unique('users')->ignore($this->user()->id),
            ],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max

             // Profile attributes (optional)
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'native_language' => ['nullable', 'string', 'max:100'],
            'english_level' => ['nullable', 'string', 'max:60'],
            'discord_id' => ['nullable', 'string', 'max:100'],
        ];
    }
}