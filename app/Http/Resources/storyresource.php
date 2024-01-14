<?php

namespace App\Http\Resources;

use App\Models\category;
use App\Models\Stories;
use App\Models\writer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class storyresource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            'heading'=>$this->heading,
            'presummary'=>$this->presummary,
            'category_id'=>category::where(['id'=>$this->category_id])->first()->name,
            'writer_id'=>writer::where(['id'=>$this->writer_id])->first()->name,
            'read_time'=>$this->read_time,
            'main_image'=>$this->main_image,
            'keypoint'=>$this->keypoint,
            'thumbnail'=>$this->thumbnail,
            'summary'=>$this->summary,
            'body'=>$this->body,
            'sub_categories_id'=>$this->sub_categories_id,
            'no_time_viewed'=>$this->no_time_viewed,
            'schedule_story_time'=>$this->schedule_story_time,
            'status'=>$this->status,
            "created_at"=>$this->created_at
        ];
    }
}
