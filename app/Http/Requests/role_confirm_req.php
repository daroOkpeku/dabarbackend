<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
class role_confirm_req extends FormRequest
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
        'email'=>'required|email',
        'verification_code'=>'required|string',
        'role'=>'required|string',
        "password"=> ['required', 'confirmed', 'string', Password::min(8)->letters()->numbers()->symbols()],
    ];
    }
}
