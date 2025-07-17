<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateMatchRequest extends FormRequest
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
            'initiator_availability_id' => 'required|integer|exists:availabilities,id',
            'receiver_availability_id' => 'required|integer|exists:availabilities,id|different:initiator_availability_id',
            'restaurant_id' => 'required|integer|exists:restaurants,id',
            'proposed_datetime' => 'required|date|after:now',
            'message' => 'nullable|string|max:500',
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
            'initiator_availability_id.required' => 'L\'ID de disponibilité de l\'initiateur est obligatoire.',
            'initiator_availability_id.integer' => 'L\'ID de disponibilité de l\'initiateur doit être un nombre entier.',
            'initiator_availability_id.exists' => 'La disponibilité de l\'initiateur n\'existe pas.',
            'receiver_availability_id.required' => 'L\'ID de disponibilité du destinataire est obligatoire.',
            'receiver_availability_id.integer' => 'L\'ID de disponibilité du destinataire doit être un nombre entier.',
            'receiver_availability_id.exists' => 'La disponibilité du destinataire n\'existe pas.',
            'receiver_availability_id.different' => 'Les disponibilités de l\'initiateur et du destinataire doivent être différentes.',
            'restaurant_id.required' => 'L\'ID du restaurant est obligatoire.',
            'restaurant_id.integer' => 'L\'ID du restaurant doit être un nombre entier.',
            'restaurant_id.exists' => 'Le restaurant n\'existe pas.',
            'proposed_datetime.required' => 'La date et l\'heure proposées sont obligatoires.',
            'proposed_datetime.date' => 'La date et l\'heure proposées doivent être une date valide.',
            'proposed_datetime.after' => 'La date et l\'heure proposées doivent être dans le futur.',
            'message.max' => 'Le message ne peut pas dépasser 500 caractères.',
        ];
    }
}
