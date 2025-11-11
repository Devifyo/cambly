<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- 1. Import Rule
use Illuminate\Support\Facades\Auth; // <-- 2. Import Auth

class ContactFormRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {   
        return [
            // 3. Make name/email required ONLY if the user is a guest
            'name' => [
                Rule::requiredIf(Auth::guest()),
                'string',
                'max:255'
            ],
            'email' => [
                Rule::requiredIf(Auth::guest()),
                'email',
                'max:255'
            ],
            
            // These are always required
            'phone_number' => 'required|string|min:7|max:25',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
        ];
    }
}