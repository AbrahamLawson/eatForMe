<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class MarkMessageAsReadRequest extends FormRequest
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
            'message_id' => 'required|integer|exists:messages,id',
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
            'message_id.required' => 'L\'identifiant du message est requis.',
            'message_id.integer' => 'L\'identifiant du message doit être un nombre entier.',
            'message_id.exists' => 'Le message spécifié n\'existe pas.',
        ];
    }
}
