<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class GetConversationRequest extends FormRequest
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
            'match_id' => 'required|integer|exists:matches,id',
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
            'match_id.required' => 'L\'identifiant du match est requis.',
            'match_id.integer' => 'L\'identifiant du match doit être un nombre entier.',
            'match_id.exists' => 'Le match spécifié n\'existe pas.',
            'page.integer' => 'Le numéro de page doit être un nombre entier.',
            'page.min' => 'Le numéro de page doit être au moins :min.',
            'per_page.integer' => 'Le nombre d\'éléments par page doit être un nombre entier.',
            'per_page.min' => 'Le nombre d\'éléments par page doit être au moins :min.',
            'per_page.max' => 'Le nombre d\'éléments par page ne peut pas dépasser :max.',
        ];
    }
}
