<?php

namespace App\Http\Resources\V2\Course;

use App\Http\Resources\Admin\VideoResource;
use Illuminate\Http\Resources\Json\JsonResource;
use function Symfony\Component\Translation\t;

class LessonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'status' => $this->status,
            'success' => true,
            'message' => 'Darslar muvaffaqiyatli keldi',
            'id' => $this->id,
            'name' => $this->name,
            'course_id' => $this->course_id,
            'files' => $this->files ? $this->files[0]->original_url : '',
        ];
    }
}
