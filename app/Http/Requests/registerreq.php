<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
class registerreq extends FormRequest
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
            "firstname"=>"required|string",
            "lastname"=>"required|string",
            "email"=>"required|email|unique:users",
            // "verification_code"=>'required|string',
            "role"=>'string',
            "password"=> ['required', 'confirmed', 'string', Password::min(8)->letters()->numbers()->symbols()],
        ];
    }
}
