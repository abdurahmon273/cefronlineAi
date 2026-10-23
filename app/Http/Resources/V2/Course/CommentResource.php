<?php

namespace App\Http\Resources\V2\Course;

use App\Http\Resources\Admin\VideoResource;
use App\Models\Video;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id'=>$this->id,
            'user_id'=>$this->user ? $this->user->id   : null,
            'fullname'=> $this->user ? ($this->user->name).' '.($this->user->surname) : "O'chirilgan xisob",
            'date' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'image'=> $this->user ? $this->user->image  : request()->getSchemeAndHttpHost().asset('delete_user.png'),
            'comment'=>$this->comment,
        ];
    }
}
