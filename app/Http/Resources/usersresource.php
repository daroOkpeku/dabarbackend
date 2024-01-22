<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class usersresource extends JsonResource
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
            "firstname"=>$this->firstname,
            "lastname"=>$this->lastname,
            "email"=>$this->email,
            "role"=>$this->role,
            "status"=>$this->status,
            'username'=>optional(User::where('user_id', $this->id)->first())->username??"",
            "phone"=>optional(User::where('user_id', $this->id)->first())->phone??"",
        ];
    }
}
