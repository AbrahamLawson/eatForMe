<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActiveSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Tout utilisateur authentifié peut faire une recherche active
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'distance' => ['required', 'numeric', 'min:1', 'max:20'],
            'activity' => ['required', 'string', 'in:eat,drink,chat'],
            'profile' => ['nullable', 'string', 'in:any,male,female'],
        ];
    }
}
