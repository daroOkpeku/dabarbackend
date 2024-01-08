<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;
class Stories extends Model
{
    use HasFactory;
    use SearchableTrait;

    protected $searchable  = [
        "columns"=>[
           "stories.heading"=>10,
            "stories.presummary"=>10,
            "stories.summary"=>10,
        ]
      ];

    protected $fillable = [
        'heading',
         'presummary',
         'category_id',
         'writer_id',
         'read_time',
         'main_image',
         'keypoint',
         'thumbnail',
         'summary',
         'body',
         'sub_categories_id',
         'no_time_viewed'
    ];
}
