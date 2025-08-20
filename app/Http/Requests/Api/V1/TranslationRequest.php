<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class TranslationRequest extends FormRequest
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
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'value' => ['required', 'string'],
            'namespace' => ['nullable', 'string', 'max:64'],
            'tags' => ['array'],
            'tags.*' => ['string', 'exists:tags,name'],
        ];
    }
}
