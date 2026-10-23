<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Notification extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public $fillable = [
        'title',
        'text',
        'link',
        'data'
    ];

    protected $appends = [
        'image',
        'web_image'
    ];

    public function user()
    {
        return $this->belongsToMany(User::class, 'user_notification')->withPivot('read_at');
    }

    public function getImageAttribute()
    {
        $file = $this->getMedia('image')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->original_url = $file->getUrl();
        }

        return $file;
    }

    public function getWebImageAttribute()
    {
        $file = $this->getMedia('web_image')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->original_url = $file->getUrl();
        }

        return $file;
    }
}
