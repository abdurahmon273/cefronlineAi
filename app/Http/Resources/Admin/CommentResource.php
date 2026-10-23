<?php

namespace App\Http\Resources\Admin;

use App\Models\Video;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        $video = $this->relationLoaded('video')
            ? $this->video
            : Video::withCount('videoWatched')->find((int) $this->video_id);

        return [
            'success'=>true,
            'message'=>"Commentlar keldi",
            'id'=>$this->id,
            'comments'=>$this->comment,
            'video'=>$video ? new VideoResource($video) : null,
            'user'=>[
                'id'=>  $this->user ? $this->user->id   : null,
                'name'=> $this->user ?$this->user->name : "O'chirilgan xisob",
                'surname'=> $this->user ?$this->user->surname : "O'chirilgan xisob",
                'image'=> $this->user ? $this->user->image  : request()->getSchemeAndHttpHost().asset('delete_user.png'),
            ]
        ];
    }
}
