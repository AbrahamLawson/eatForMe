<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class FindRestaurantsByLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:0.1|max:50',
            'cuisine_types' => 'nullable|array',
            'cuisine_types.*' => 'string',
            'price_ranges' => 'nullable|array',
            'price_ranges.*' => 'integer|min:1|max:4',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'latitude.required' => 'La latitude est obligatoire.',
            'latitude.numeric' => 'La latitude doit être un nombre.',
            'latitude.between' => 'La latitude doit être entre -90 et 90.',
            'longitude.required' => 'La longitude est obligatoire.',
            'longitude.numeric' => 'La longitude doit être un nombre.',
            'longitude.between' => 'La longitude doit être entre -180 et 180.',
            'radius.required' => 'Le rayon est obligatoire.',
            'radius.numeric' => 'Le rayon doit être un nombre.',
            'radius.min' => 'Le rayon doit être d\'au moins 0.1 km.',
            'radius.max' => 'Le rayon ne peut pas dépasser 50 km.',
            'cuisine_types.array' => 'Les types de cuisine doivent être une liste.',
            'price_ranges.array' => 'Les gammes de prix doivent être une liste.',
            'price_ranges.*.integer' => 'Chaque gamme de prix doit être un nombre entier.',
            'price_ranges.*.min' => 'Chaque gamme de prix doit être au moins 1.',
            'price_ranges.*.max' => 'Chaque gamme de prix ne peut pas dépasser 4.',
            'limit.integer' => 'La limite doit être un nombre entier.',
            'limit.min' => 'La limite doit être au moins 1.',
            'limit.max' => 'La limite ne peut pas dépasser 100.',
            'offset.integer' => 'Le décalage doit être un nombre entier.',
            'offset.min' => 'Le décalage doit être au moins 0.',
        ];
    }
}
