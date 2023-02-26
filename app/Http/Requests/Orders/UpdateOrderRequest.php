<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\ValidationFormRequestAPI;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
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
            'order_date' => 'nullable|date',
            'shipping_date' => 'nullable|date',
            'table_no' => 'nullable|numeric',
            'location' => [
                'nullable',
                Rule::in(['home', 'restaurant'])
            ],
            'status' => [
                'nullable',
                Rule::in(['pending', 'prepare', 'shipping', 'finish']),
            ]
        ];
    }
}
