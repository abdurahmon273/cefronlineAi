<?php

namespace App\Http\Resources;

use App\Http\Resources\Admin\NewsResource;
use App\Http\Resources\Admin\SavedLessonResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NewsApiResourceCollection extends ResourceCollection
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
            return [
                'group_date'=>Carbon::parse($group->created_at)->format('d-m-Y'),
//            [ 'id'=>$this->id,
//                'title'=>$this->title,
//                'description'=>$this->description,
//                'status'=>$this->status ? 'video' : 'images',
//                'video_link'=>$this->video_link,
//                'images'=>$this->image ? $this->image->url : null,
//                'created_time'=>Carbon::parse($this->created_at)->timezone('Asia/Tashkent')->format('H:i')]

            ];
        })->toArray();
//        return [
//            'group_date'=>Carbon::parse($this->created_at)->format('d-m-Y'),
//            [ 'id'=>$this->id,
//                'title'=>$this->title,
//                'description'=>$this->description,
//                'status'=>$this->status ? 'video' : 'images',
//                'video_link'=>$this->video_link,
//                'images'=>$this->image ? $this->image->url : null,
//                'created_time'=>Carbon::parse($this->created_at)->timezone('Asia/Tashkent')->format('H:i')]

//        ];


    }
}
