<?php

namespace App\Livewire\User\Lessons;

use App\Models\Comment;
use App\Models\SavedLesson;
use App\Models\Video;
use App\Models\VideoWached;
use Livewire\Component;

class LessonLivewire extends Component
{
    public $videoLessons;
    public $videoLessonFirst;
    public $comments;
    public $is_saved;
    public $commentText = '';

    public function mount($id, $video_id = null)
    {
        $this->videoLessons = Video::with('lesson.course')->where('lesson_id', $id)->get();
        if($this->videoLessons->isEmpty()){
            return;
        }

        $this->videoLessonFirst = $this->videoLessons->firstWhere('id', $video_id) ?? $this->videoLessons->first();

        $this->loadVideoState($this->videoLessonFirst->id);

        $userId=auth()->id();

        $existingWatch = VideoWached::where([
            'course_id' => $this->videoLessonFirst->lesson->course_id,
            'lesson_id' => $this->videoLessonFirst->lesson_id,
            'video_id' => $this->videoLessonFirst->id,
            'user_id' => $userId
        ])->first();

        if (!$existingWatch && $userId) {
            $watchRecord = VideoWached::create([
                'video_id' =>$this->videoLessonFirst->id ,
                'course_id' => $this->videoLessonFirst->lesson->course_id,
                'lesson_id' => $this->videoLessonFirst->lesson_id,
                'user_id' => $userId
            ]);
        }


    }

    public function isYouTube()
    {
        return $this->videoLessonFirst && (str_contains($this->videoLessonFirst->link, 'youtube.com') || str_contains($this->videoLessonFirst->link, 'youtu.be'));
    }

    public function getVideoId()
    {
        if ($this->isYouTube() && $this->videoLessonFirst) {
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->videoLessonFirst->link, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public function saveLesson($id)
    {
        $userId = auth()->id();
        if (!$userId) {
            return;
        }

        $record = SavedLesson::firstOrNew([
            'user_id' => $userId,
            'video_id' => $id,
        ]);

        $record->saved = $record->saved ? 0 : 1;
        $record->save();

        $this->is_saved = (bool) $record->saved;
    }

    public function sendComment($id)
    {
        if (empty(trim($this->commentText))) {
            return;
        }

        $userId = auth()->id();
        if (!$userId) {
            return;
        }
        $video = $this->videoLessons->firstWhere('id', $id) ?? Video::find($id);
        if (!$video) {
            return;
        }

        $comment = Comment::create([
            'user_id' => $userId,
            'video_id' => $id,
            'comment' => trim($this->commentText),
        ]);

        if ($this->comments instanceof \Illuminate\Support\Collection) {
            $this->comments->prepend($comment);
        } else {
            $this->comments = collect([$comment])->concat($this->comments ?? collect());
        }

        $this->commentText = '';
    }

    public function selectVideo($videoId)
    {
        $this->videoLessonFirst = $this->videoLessons->firstWhere('id', $videoId) ?? Video::find($videoId);

        if (!$this->videoLessonFirst) {
            return;
        }

        $this->loadVideoState($videoId);
    }

    private function loadVideoState($videoId)
    {
        $this->comments = Comment::where('video_id', $videoId)->orderBy('created_at', 'desc')->get();
        $this->is_saved = SavedLesson::where('video_id', $videoId)
            ->where('user_id', auth()->id())
            ->where('saved', '1')
            ->exists();
    }

    public function render()
    {
        return view('livewire.user.lessons.lesson-livewire');
    }
}
