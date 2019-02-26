<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class CustomersRequest extends FormRequest
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
     * @return array
     */
    public function rules(Request $request)
    {
        $rules = [];
        switch ($request->method()) {
            case 'POST':
                $rules = [
                    'name'=> 'required|unique:products,name',
                    'guj_name'=> 'required',
                    'box_price'=> 'required|unique:customers,mobile',
                    'photo'=> 'image',
                ];
                break;
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name is required.!',
            'guj_name.required' => "Product's gujarati name is required.!",
            'name.unique' => 'This product already exists.!'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
            'data' => [],
        ], 422);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
