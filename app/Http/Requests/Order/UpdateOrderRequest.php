<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'image_svg_text' => 'nullable|string|max:5120',
            'name' => 'sometimes|string',
            'email' => 'sometimes|email',
            'phone_number' => 'sometimes|string',
            'quantity' => 'nullable|integer',
            'quantity_per_unit' => 'nullable|string',
            'status' => 'sometimes|string',
            'refer_by' => 'sometimes|string',
            'notes' => 'nullable|string',
            'message' => 'nullable|string',
        ];
    }
}
