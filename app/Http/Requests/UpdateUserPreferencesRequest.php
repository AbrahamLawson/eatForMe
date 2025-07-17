<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserPreferencesRequest extends FormRequest
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
            'gender_preference' => ['required', Rule::in(['male', 'female', 'both', 'any'])],
            'age_range_min' => ['required', 'integer', 'min:18', 'max:99', 'lte:age_range_max'],
            'age_range_max' => ['required', 'integer', 'min:18', 'max:99', 'gte:age_range_min'],
            'interests' => ['nullable', 'array'],
            'interests.*' => ['string', 'max:50'],
        ];
    }
}
