<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class profilecreatereq extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "lastname"=>"string",
            "firstname"=>"string",
            "username"=>'required|string',
            'phone'=>'required|min:10|max:11',
            'user_id'=>'required|integer'
        ];
    }
}
