<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class videoreq extends FormRequest
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
            "title"=>"string|nullable",
            "url"=>"string|nullable",
            "file"=>"string|nullable",
            "category"=>"string|nullable",
           "writername"=>"string|nullable",
        ];
    }
}
