<?php

namespace App\Http\Resources\Admin;

use App\Models\News;
use Carbon\Carbon;
use http\Client\Response;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray($request)
    {
            return [
                        'id' => $this->id,
                        'title' => $this->title,
                        'description' => $this->description,
                        'link' => $this->link ? $this->link : null,
                        'status' => $this->status,
                        'video_link' => $this->video_link,
                        'images' => $this->image ?  $this->image->url : null,
                        'web_image' =>$this->web_image ? $this->web_image->url : null,
                        'created_time' => $this->created_at->format('H:i'),
                    ];

//        return [
//            'group_date'=>Carbon::parse($this->created_at)->format('d-m-Y'),
//            'data'=> [ 'id'=>$this->id,
//                'title'=>$this->title,
//                'description'=>$this->description,
//                'status'=>$this->status ? 'video' : 'images',
//                'video_link'=>$this->video_link,
//                'images'=>$this->image ? $this->image->url : null,
//                'created_time'=>Carbon::parse($this->created_at)->timezone('Asia/Tashkent')->format('H:i')]
//
//        ];
    }
}
