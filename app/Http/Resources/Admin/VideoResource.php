<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\FilesResources;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class VideoResource extends JsonResource
{
    public function toArray($request)
    {
        $lesson = $this->lesson;
        $course = $lesson ? $lesson->course : null;
        $savedLesson = $this->savedLesson->where('user_id', auth('sanctum')->id())->first();
        $view_count = $this->video_watched_count ?? 0;
        return [
            'id'=>$this->id,
            'status'=>$this->status,
            'name'=>$this->name,
            'link'=>$this->link,
            'description'=>$this->description,
            'lesson'=>$lesson ? $lesson->name : null,
            'course'=>$course ? $course->name : null,
            'lesson_id'=>$lesson ? $lesson->id : null,
            'course_id'=>$course ? $course->id : null,
            'saved'=>isset($savedLesson->saved) ? (int) $savedLesson->saved == 1 : false,
            'liked'=>isset($savedLesson->liked) ? (int) $savedLesson->liked == 1 : false,
            'view_count'=>$view_count,
            'files'=>FilesResources::collection($this->files ?: [])
        ];
    }
}
