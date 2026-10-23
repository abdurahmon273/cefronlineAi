<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SavedLesson extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'saved_lessons';

    public const SAVED_SELECT = [
        '0' => 'yo\'q',
        '1' => 'ha',
    ];

    public const LIKED_SELECT = [
        '0' => 'yo\'q',
        '1' => 'ha',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'user_id',
        'saved',
        'liked',
        'video_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }
}
