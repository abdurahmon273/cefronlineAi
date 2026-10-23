<?php

namespace App\Http\Resources\V2\Course;

use App\Http\Resources\Admin\VideoResource;
use App\Models\Video;
use Illuminate\Http\Resources\Json\JsonResource;

class SavedLessonResource extends JsonResource
{
    public function toArray($request)
    {
        $video=Video::find($this->video_id);
        return [
            'success'=>true,
            'id'=>$this->id,
            'saved'=>$this->saved ? 'Saqlangan' : 'Saqlanmagan',
            'video'=>new VideoResource($video),
            'created_at' => $this->created_at->toDateString()
//            'user_id'=>$this->user_id,
        ];
    }
}
