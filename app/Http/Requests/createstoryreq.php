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
         'heading'=>'string',
         'presummary'=>'string',
         'category_id'=>'regex:/^[0-9 ]*$/',
         'writer_id'=>'regex:/^[0-9 ]*$/',
         'read_time'=>'regex:/^[a-zA-Z0-9- ]*$/',
          'main_image'=>'string',
          'keypoint'=>'string',
          'thumbnail'=>'string',
          'summary'=>'string',
          'body'=>'string',
          'stories_section'=>'string',
          //'sub_categories_id'=>'regex:/^[0-9 ]*$/',
          //'no_time_viewed'=>'regex:/^[0-9 ]*$/',
        //  'schedule_story_time'=>'date_format:Y-m-d H:i:s|timezone:UTC',
         'schedule_story_time' => 'string',
           'status'=>'integer'
        ];
    }
}
