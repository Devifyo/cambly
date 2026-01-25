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
        $minDate = now()->subYears($minAge)->toDateString();
        $isStudent = $this->routeIs('student.profile.update');
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user()->id),
            ],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            
            // Logical fix: If route is student.profile.update, it's nullable. 
            // Otherwise (like for teachers), it's required.
            'date_of_birth' => $isStudent
            ? ['required', 'date']
            : ['nullable', 'date'],
            'native_language' => ['nullable', 'string', 'max:100'],
            'english_level' => [
                'required', 
                Rule::in(['native-like', 'fluent', 'conversational', 'basic', 'none']),
            ],
            'country_residence' => ['required', 'string', 'max:100'],
        ];
    }


    public function messages(): array
    {
        return [
            'date_of_birth.before_or_equal' => 'You must be at least 13 years old to register.',
            'date_of_birth.date' => 'The date of birth must be a valid date format (YYYY-MM-DD).',
            'english_level.in' => 'The selected English level is invalid.',
            'avatar.max' => 'The profile photo cannot exceed 5MB.',
            'zoom_link.required' => 'Please provide your Zoom Personal Meeting Link.',
            'zoom_link.url' => 'Please enter a valid URL for your Zoom Personal Meeting Link.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        dd(request()->all(), $validator->errors()->toArray());
    }
}