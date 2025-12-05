<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Anyone authenticated can try, policy/controller handles specifics
    }

    public function rules(): array
    {   
        $minAge = 13;
        
        // Calculate the maximum allowed date of birth (Today minus 13 years)
        $minDate = now()->subYears($minAge)->format('Y-m-d');
        return [
            'name' => 'required|string|max:255',
            // 'gender' => 'required',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // This rule checks if the email is unique, BUT ignores the current user's ID
                Rule::unique('users')->ignore($this->user()->id),
            ],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max

            //  Profile attributes (optional)
            // 'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'date_of_birth' => [
                // 'required',
                'date',
                'before_or_equal:' . now()->toDateString(), // Cannot be in the future
                // Checks if the date of birth is at least 13 years ago
                'before_or_equal:' . $minDate, 
            ],
            'native_language' => ['nullable', 'string', 'max:100'],
            'english_level' => [
                'required', 
                Rule::in(['native-like', 'fluent', 'conversational', 'basic', 'none']),
            ],
            'country_residence' => ['required', 'string', 'max:100'],
            'discord_id' => ['nullable', 'string', 'max:100'],
        ];
    }


    public function messages(): array
    {
        return [
            'date_of_birth.before_or_equal' => 'You must be at least 13 years old to register.',
            'date_of_birth.date' => 'The date of birth must be a valid date format (YYYY-MM-DD).',
            'english_level.in' => 'The selected English level is invalid.',
            'avatar.max' => 'The profile photo cannot exceed 5MB.',
        ];
    }

    // protected function failedValidation(Validator $validator)
    // {
    //     dd($validator->errors()->toArray());
    // }
}