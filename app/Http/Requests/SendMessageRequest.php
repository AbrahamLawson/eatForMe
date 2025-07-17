<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SendMessageRequest extends FormRequest
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
            'receiver_id' => 'required|integer|exists:users,id',
            'content' => 'required|string|min:1|max:1000',
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
            'receiver_id.required' => 'L\'identifiant du destinataire est requis.',
            'receiver_id.integer' => 'L\'identifiant du destinataire doit être un nombre entier.',
            'receiver_id.exists' => 'Le destinataire spécifié n\'existe pas.',
            'content.required' => 'Le contenu du message est requis.',
            'content.string' => 'Le contenu du message doit être une chaîne de caractères.',
            'content.min' => 'Le contenu du message doit contenir au moins :min caractère.',
            'content.max' => 'Le contenu du message ne peut pas dépasser :max caractères.',
        ];
    }
}
