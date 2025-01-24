<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            "product_name" => "required|string",
            "product_description" => "required|string",
            "product_price" => "required|numeric",
            "product_images" => "required|array|min:1",
            "product_images.*" => "required|image|mimes:jpeg,png,jpg,gif|max:2048",
            "product_colors" => "required|array|min:1",
            "product_colors.*" => "string|max:7",
            "product_video" => "nullable|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:10240",
            "product_quantity" => "required|integer|min:0",
            "weight" => "nullable|numeric",
            "size" => "nullable|numeric",
            "category_id" => "required|exists:categories,id",
        ];
    }
}
