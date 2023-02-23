<?php

namespace App\Http\Requests\Products;

// use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\ValidationFormRequestAPI;

class CreateProductRequest extends ValidationFormRequestAPI
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|max:100',
            'description' => 'nullable|max:498',
            'unit_price' => 'required|numeric',
            'in_stock' => 'required|numeric',
            'image' => 'required',
            'image.*' => 'image|mimes:png,jpg,jpeg,svg|max:2048',
            'category_id' => 'required|numeric'
        ];
    }
}
