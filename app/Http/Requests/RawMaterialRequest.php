<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RawMaterialRequest extends FormRequest
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
            'unit' => ['required', 'string', Rule::in(['gram', 'kg', 'meter', 'pcs', 'liter'])],
            'unit_price' => ['required', 'numeric', 'min:0', 'decimal:0,4'],
            'wastage_percent' => ['required', 'numeric', 'between:0,100', 'decimal:0,2'],
        ];
    }

    /**
     * Get the validation messages for the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama bahan baku wajib diisi.',
            'name.max' => 'Nama bahan baku maksimal 255 karakter.',
            'unit.required' => 'Satuan wajib dipilih.',
            'unit.in' => 'Satuan harus gram, kg, meter, pcs, atau liter.',
            'unit_price.required' => 'Harga satuan wajib diisi.',
            'unit_price.numeric' => 'Harga satuan harus berupa angka.',
            'unit_price.min' => 'Harga satuan tidak boleh negatif.',
            'wastage_percent.required' => 'Persentase susut wajib diisi.',
            'wastage_percent.between' => 'Persentase susut harus antara 0 sampai 100.',
        ];
    }
}
