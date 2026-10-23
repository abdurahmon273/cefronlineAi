<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Video;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SeoController extends Controller
{
    public function sitemap()
    {
        $sitemap = Sitemap::create();

        $staticPages = [
            ['url' => route('user.dashboard'), 'priority' => 1.0, 'frequency' => Url::CHANGE_FREQUENCY_DAILY],
            ['url' => route('login'), 'priority' => 0.6, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['url' => route('register'), 'priority' => 0.7, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['url' => route('forgot.form'), 'priority' => 0.4, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['url' => route('user.lessons'), 'priority' => 0.9, 'frequency' => Url::CHANGE_FREQUENCY_DAILY],
            ['url' => route('user.practices'), 'priority' => 0.9, 'frequency' => Url::CHANGE_FREQUENCY_DAILY],
            ['url' => route('user.market'), 'priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_DAILY],
            ['url' => route('user.contact'), 'priority' => 0.6, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
        ];

        foreach ($staticPages as $page) {
            $sitemap->add(
                Url::create($page['url'])
                    ->setLastModificationDate(now())
                    ->setChangeFrequency($page['frequency'])
                    ->setPriority($page['priority'])
            );
        }

        Course::query()
            ->where('published', 1)
            ->orderBy('order_id')
            ->get(['id', 'updated_at'])
            ->each(function (Course $course) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('user.lessons.detail', $course->id))
                        ->setLastModificationDate($course->updated_at ?? now())
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.8)
                );
            });

        Lesson::query()
            ->orderBy('order_id')
            ->get(['id', 'updated_at'])
            ->each(function (Lesson $lesson) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('user.lesson', ['id' => $lesson->id]))
                        ->setLastModificationDate($lesson->updated_at ?? now())
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.7)
                );
            });

        Video::query()
            ->orderBy('order_id')
            ->get(['id', 'lesson_id', 'updated_at'])
            ->each(function (Video $video) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('user.lesson', ['id' => $video->lesson_id, 'video_id' => $video->id]))
                        ->setLastModificationDate($video->updated_at ?? now())
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.6)
                );
            });

        return $sitemap;
    }
}
