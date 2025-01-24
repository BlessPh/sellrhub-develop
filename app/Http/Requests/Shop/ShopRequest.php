<?php

namespace App\Http\Requests\Shop;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ShopRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:shops,name|max:255',
            'description' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_url' => 'nullable|array|min:1',
            'image_url.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'payment_method' => 'nullable|array',
            'payment_method.*' => 'exists:payment_method,id',
            'delivery_type' => 'nullable|array',
            'delivery_type.*' => 'exists:delivery_types,id',
        ];
    }
}
