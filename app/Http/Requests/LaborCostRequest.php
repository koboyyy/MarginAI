<?php

namespace App\Http\Requests;

use App\Enums\LaborRateType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LaborCostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'rate_type' => ['required', Rule::enum(LaborRateType::class)],
            'rate' => ['required', 'numeric', 'min:0', 'decimal:0,4'],
        ];
    }
}
