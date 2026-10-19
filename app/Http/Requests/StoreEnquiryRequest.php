<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnquiryRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'opportunity_id' => ['nullable', 'exists:opportunities,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\s\-]{10,15}$/'],
            'email' => ['required', 'email', 'max:255'],
            'target_state' => ['required', 'string', 'max:10'],
            'target_city' => ['required', 'string', 'max:100'],
            'budget_range' => ['required', 'string', 'in:under-5l,5l-15l,15l-30l,30l-plus'],
            'preferred_timeline' => ['required', 'string', 'in:immediate,1-3-months,3-6-months'],
            'commercial_space_status' => ['required', 'string', 'in:owned,leased,searching'],
            'carpet_area_sqft' => ['nullable', 'integer', 'min:50', 'max:50000'],
            'business_experience' => ['required', 'string', 'in:first_time,existing_retail,corporate_professional,other'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'consent' => ['accepted'], // Non-negotiable mandatory explicit consent
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'consent.accepted' => 'You must authorize Franchise Dekho to coordinate your application before submitting your details.',
            'phone.regex' => 'Please provide a valid 10-digit mobile number.',
        ];
    }
}
