<?php

namespace App\Models;
use App\Traits\StoreTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tending extends Model
{
    use HasFactory, StoreTrait;
    protected $fillable = [
        'stories_id'
    ];
}
