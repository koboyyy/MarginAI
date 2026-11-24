<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePriceSimulationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $businessId = $this->user()?->business()->value('id');

        return [
            'simulated_price' => ['required', 'string', 'min:0', 'decimal:0,2'],
            'estimated_units_sold' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'material_price_changes' => ['sometimes', 'array'],
            'material_price_changes.*.raw_material_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('raw_materials', 'id')->where('business_id', $businessId),
            ],
            'material_price_changes.*.change_percent' => [
                'required',
                'string',
                'decimal:0,2',
                'min:-100',
            ],
            'competitor_prices' => ['sometimes', 'array', 'min:1', 'max:20'],
            'competitor_prices.*' => ['required', 'string', 'decimal:0,2', 'gt:0'],
        ];
    }
}
