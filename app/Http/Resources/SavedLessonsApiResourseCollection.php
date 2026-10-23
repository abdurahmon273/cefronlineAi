<?php

namespace App\Http\Resources;

use App\Http\Resources\Admin\SavedLessonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SavedLessonsApiResourseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Kolleksiyani vaqt bo'yicha guruhlash
        $saved = $this->collection->groupBy(function($item) {
            return $item->created_at->toDateString(); // Y-m-d formatida
        });
        // Har bir guruhni alohida formatlash
        return $saved->map(function($group) {
            return SavedLessonResource::collection($group);
        })->toArray();
    }
}
