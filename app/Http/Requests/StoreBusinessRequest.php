<?php

namespace App\Http\Requests;

use App\Enums\BusinessCategory;
use App\Enums\PricingMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBusinessRequest extends FormRequest
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
        $pricingMethod = $this->input('pricing_method', PricingMethod::Margin->value);
        $maximumTargetPercent = $pricingMethod === PricingMethod::Markup->value ? '999.99' : '99.99';

        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::enum(BusinessCategory::class)],
            'pricing_method' => ['sometimes', Rule::enum(PricingMethod::class)],
            'default_margin_percent' => ['required', 'numeric', 'min:0', "max:{$maximumTargetPercent}", 'decimal:0,2'],
        ];
    }
}
