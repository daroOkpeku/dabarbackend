<?php

namespace App\Http\Resources;

use App\Models\category;
use App\Models\writer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class uniquecategory extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'heading'=>$this->heading,
            'presummary'=>$this->presummary,
            'category_id'=>$this->category_id,
            'writer_id'=>$this->writer_id,
            'read_time'=>$this->read_time,
            'main_image'=>$this->main_image,
            "stories_section"=>$this->stories_section,
            'summary'=>$this->summary,
            'body'=>$this->body,
            'schedule_story_time'=>$this->schedule_story_time,
            'status'=>$this->status,
            'writer'=>optional(writer::where('id', $this->writer_id)->first())->name,
            'category'=>optional(category::where('id', $this->category_id)->first())->name,
            'created_at'=>$this->created_at

        ];
    }
}
