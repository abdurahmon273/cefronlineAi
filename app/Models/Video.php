<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Video extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;

    public $table = 'videos';
    protected $with = ['lesson.course', 'savedLesson'];
    protected $appends = [
        'files',
    ];

    public const STATUS = [
        '0' => "free",
        '1' => "premium",
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'link',
        'lesson_id',
        'description',
        'status',
        'name',
        'order_id',
        'view_count',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function getFilesAttribute()
    {
        return $this->getMedia('files');
    }

    public function savedLesson()
    {
        return $this->hasMany(SavedLesson::class);
    }
    public function videoWatched(): HasMany
    {
        return $this->hasMany(VideoWached::class, 'video_id');
    }
    public function videoWatchedCount():int
    {
        return $this->hasMany(VideoWached::class, 'video_id')->count();
    }

    public function watcheds()
    {
        return $this->belongsToMany(User::class, 'video_wacheds', 'video_id', 'user_id');
    }
    public static function updateOrder($data)
    {
        foreach ($data as $datum) {
            $id = $datum['value'];
            $displayIndex = $datum['order'];

            static::where('id', $id)->update(['order_id' => $displayIndex]);
        }
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'video_id');
    }
}
