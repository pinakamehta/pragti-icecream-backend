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
                    'full_name'=> 'required',
                    'mobile'=> 'required|unique:customers,mobile',
                    'balance'=> 'required',
                    'photo'=> 'image',
                ];
                break;
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'full_name.required' => 'Full name is required.!',
            'mobile.required' => 'Mobile number is required.!',
            'mobile.unique' => 'Mobile number has been already taken.!'
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
