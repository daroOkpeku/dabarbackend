<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sociallink extends Model
{
    use HasFactory;

    protected $fillable = [
        'twitter',
         'instagram',
         'linkedin',
         'user_id',

    ];
}
