<?php

namespace App\Http\Requests;

use App\Enums\OverheadCostType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OverheadCostRequest extends FormRequest
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
            'type' => ['required', Rule::enum(OverheadCostType::class)],
            'amount' => ['required', 'numeric', 'min:0', 'decimal:0,4'],
        ];
    }
}
