<?php

namespace App\Http\Requests\Orders;

// use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\ValidationFormRequestAPI;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends ValidationFormRequestAPI
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
            'order_date' => 'required|date',
            'shipping_date' => 'required|date',
            'table_no' => 'required|numeric',
            'location' =>  [
                'required',
                Rule::in(['home', 'restaurant'])
            ],
            'products' => 'required|array'
        ];
    }
}
