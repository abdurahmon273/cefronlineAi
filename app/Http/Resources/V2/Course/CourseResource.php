<?php

namespace App\Http\Resources\V2\Course;

use App\Models\Lesson;
use App\Models\Video;
use App\Models\VideoWached;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request)
    {
        $percentage = 0;
        if (auth()->user()){

        $watched = VideoWached::where('course_id', $this->id)->where('user_id',auth()->user()->id)->get()->count();
        if ($watched) {
            $courseId = $this->id;
            $totalVideos = Video::whereHas('lesson', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })->count();
            if ($totalVideos > 0) {
                $percentage = floatval($watched) / floatval($totalVideos) * 100;
            }
        }
        }

        return [
            'status' => $this->status,
            'success' => true,
            'message' => "Kurslar keldi",
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'lessons_count' => $this->lessons->count(),
            'images' => $this->image->url,
            'lesson_title' =>  $this->lessons->where('order_id', 1)->first()
                ? $this->lessons->where('order_id', 1)->first()->name
                : ($this->lessons->first() ? $this->lessons->first()->name : ''),
            'percentage' => $percentage
        ];
    }
}
