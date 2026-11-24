<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $isCreating = $this->isMethod('post');
        $businessId = $this->user()?->business()->value('id');
        $required = $isCreating ? 'required' : 'sometimes';

        return [
            'name' => [$required, 'string', 'max:255'],
            'sku' => ['sometimes', 'nullable', 'string', 'max:100'],
            'batch_yield' => [$required, 'numeric', 'gt:0', 'decimal:0,2'],
            'batches_per_month' => [$required, 'numeric', 'gt:0', 'decimal:0,2'],
            'selling_price' => ['sometimes', 'nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'materials' => $isCreating ? ['required', 'array', 'min:1'] : ['sometimes', 'array'],
            'materials.*.raw_material_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('raw_materials', 'id')->where('business_id', $businessId),
            ],
            'materials.*.quantity_used' => ['required', 'numeric', 'gt:0', 'decimal:0,4'],
            'labor_costs' => ['sometimes', 'array'],
            'labor_costs.*.labor_cost_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('labor_costs', 'id')->where('business_id', $businessId),
            ],
            'labor_costs.*.quantity_used' => ['required', 'numeric', 'gt:0', 'decimal:0,4'],
            'overhead_costs' => ['sometimes', 'array'],
            'overhead_costs.*.overhead_cost_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('overhead_costs', 'id')->where('business_id', $businessId),
            ],
        ];
    }
}
