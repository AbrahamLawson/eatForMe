<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ListAvailabilitiesRequest extends FormRequest
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
            'distance' => ['nullable', 'numeric', 'min:0'],
            'cuisine' => ['nullable', 'string'],
            'payment' => ['nullable', 'string', 'in:dutch,invite,invited'],
        ];
    }
    
    /**
     * Get the filters from the request.
     *
     * @return array
     */
    public function filters(): array
    {
        return $this->only(['distance', 'cuisine', 'payment']);
    }
}
