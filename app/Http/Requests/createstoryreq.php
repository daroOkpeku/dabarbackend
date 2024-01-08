<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class createstoryreq extends FormRequest
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
         'heading'=>'required|regex:/^[a-zA-Z0-9- ]*$/',
         'presummary'=>'required|regex:/^[a-zA-Z0-9- ]*$/',
         'category_id'=>'required|regex:/^[0-9 ]*$/',
         'writer_id'=>'required|regex:/^[0-9 ]*$/',
         'read_time'=>'required|regex:/^[a-zA-Z0-9- ]*$/',
          'main_image'=>'required|string',
          'keypoint'=>'required|string',
          'thumbnail'=>'string',
          'summary'=>'string',
          'body'=>'string',
          'sub_categories_id'=>'required|regex:/^[0-9 ]*$/',
          'no_time_viewed'=>'regex:/^[0-9 ]*$/'
        ];
    }
}
