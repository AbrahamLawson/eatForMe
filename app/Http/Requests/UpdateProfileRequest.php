<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateProfileRequest extends FormRequest
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
            'bio' => 'nullable|string|max:500',
            'preferences' => 'nullable|array',
            'preferences.*' => 'string',
            'dietary_restrictions' => 'nullable|array',
            'dietary_restrictions.*' => 'string',
            'favorite_cuisines' => 'nullable|array',
            'favorite_cuisines.*' => 'string',
            'avatar_url' => 'nullable|string|url|max:255',
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
            'bio.max' => 'La biographie ne peut pas dépasser 500 caractères.',
            'preferences.array' => 'Les préférences doivent être une liste.',
            'dietary_restrictions.array' => 'Les restrictions alimentaires doivent être une liste.',
            'favorite_cuisines.array' => 'Les cuisines favorites doivent être une liste.',
            'avatar_url.url' => 'L\'URL de l\'avatar doit être une URL valide.',
            'avatar_url.max' => 'L\'URL de l\'avatar ne peut pas dépasser 255 caractères.',
        ];
    }
}
