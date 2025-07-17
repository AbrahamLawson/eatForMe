<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class GetProfilesByPreferencesRequest extends FormRequest
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
            'preferences' => 'sometimes|array',
            'preferences.*' => 'string',
            'dietary_restrictions' => 'sometimes|array',
            'dietary_restrictions.*' => 'string',
            'favorite_cuisines' => 'sometimes|array',
            'favorite_cuisines.*' => 'string',
            'max_distance' => 'sometimes|numeric|min:0',
            'latitude' => 'required_with:max_distance|numeric',
            'longitude' => 'required_with:max_distance|numeric',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:50',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'preferences.array' => 'Les préférences doivent être un tableau.',
            'preferences.*.string' => 'Les préférences doivent être des chaînes de caractères.',
            'dietary_restrictions.array' => 'Les restrictions alimentaires doivent être un tableau.',
            'dietary_restrictions.*.string' => 'Les restrictions alimentaires doivent être des chaînes de caractères.',
            'favorite_cuisines.array' => 'Les cuisines préférées doivent être un tableau.',
            'favorite_cuisines.*.string' => 'Les cuisines préférées doivent être des chaînes de caractères.',
            'max_distance.numeric' => 'La distance maximale doit être un nombre.',
            'max_distance.min' => 'La distance maximale doit être au moins :min.',
            'latitude.required_with' => 'La latitude est requise lorsque la distance maximale est spécifiée.',
            'latitude.numeric' => 'La latitude doit être un nombre.',
            'longitude.required_with' => 'La longitude est requise lorsque la distance maximale est spécifiée.',
            'longitude.numeric' => 'La longitude doit être un nombre.',
            'page.integer' => 'Le numéro de page doit être un nombre entier.',
            'page.min' => 'Le numéro de page doit être au moins :min.',
            'per_page.integer' => 'Le nombre d\'éléments par page doit être un nombre entier.',
            'per_page.min' => 'Le nombre d\'éléments par page doit être au moins :min.',
            'per_page.max' => 'Le nombre d\'éléments par page ne peut pas dépasser :max.',
        ];
    }
}
