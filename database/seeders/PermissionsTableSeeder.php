<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'region_create',
            ],
            [
                'id'    => 18,
                'title' => 'region_edit',
            ],
            [
                'id'    => 19,
                'title' => 'region_show',
            ],
            [
                'id'    => 20,
                'title' => 'region_delete',
            ],
            [
                'id'    => 21,
                'title' => 'region_access',
            ],
            [
                'id'    => 22,
                'title' => 'city_create',
            ],
            [
                'id'    => 23,
                'title' => 'city_edit',
            ],
            [
                'id'    => 24,
                'title' => 'city_show',
            ],
            [
                'id'    => 25,
                'title' => 'city_delete',
            ],
            [
                'id'    => 26,
                'title' => 'city_access',
            ],
            [
                'id'    => 27,
                'title' => 'setting_access',
            ],
            [
                'id'    => 28,
                'title' => 'news_create',
            ],
            [
                'id'    => 29,
                'title' => 'news_edit',
            ],
            [
                'id'    => 30,
                'title' => 'news_show',
            ],
            [
                'id'    => 31,
                'title' => 'news_delete',
            ],
            [
                'id'    => 32,
                'title' => 'news_access',
            ],
            [
                'id'    => 33,
                'title' => 'application_create',
            ],
            [
                'id'    => 34,
                'title' => 'application_edit',
            ],
            [
                'id'    => 35,
                'title' => 'application_show',
            ],
            [
                'id'    => 36,
                'title' => 'application_delete',
            ],
            [
                'id'    => 37,
                'title' => 'application_access',
            ],
            [
                'id'    => 38,
                'title' => 'shablon_create',
            ],
            [
                'id'    => 39,
                'title' => 'shablon_edit',
            ],
            [
                'id'    => 40,
                'title' => 'shablon_show',
            ],
            [
                'id'    => 41,
                'title' => 'shablon_delete',
            ],
            [
                'id'    => 42,
                'title' => 'shablon_access',
            ],
            [
                'id'    => 43,
                'title' => 'sms_access',
            ],
            [
                'id'    => 44,
                'title' => 'message_create',
            ],
            [
                'id'    => 45,
                'title' => 'message_edit',
            ],
            [
                'id'    => 46,
                'title' => 'message_show',
            ],
            [
                'id'    => 47,
                'title' => 'message_delete',
            ],
            [
                'id'    => 48,
                'title' => 'message_access',
            ],
            [
                'id'    => 49,
                'title' => 'teaching_section_access',
            ],
            [
                'id'    => 50,
                'title' => 'course_create',
            ],
            [
                'id'    => 51,
                'title' => 'course_edit',
            ],
            [
                'id'    => 52,
                'title' => 'course_show',
            ],
            [
                'id'    => 53,
                'title' => 'course_delete',
            ],
            [
                'id'    => 54,
                'title' => 'course_access',
            ],
            [
                'id'    => 55,
                'title' => 'saved_lesson_create',
            ],
            [
                'id'    => 56,
                'title' => 'saved_lesson_edit',
            ],
            [
                'id'    => 57,
                'title' => 'saved_lesson_show',
            ],
            [
                'id'    => 58,
                'title' => 'saved_lesson_delete',
            ],
            [
                'id'    => 59,
                'title' => 'saved_lesson_access',
            ],
            [
                'id'    => 60,
                'title' => 'lesson_create',
            ],
            [
                'id'    => 61,
                'title' => 'lesson_edit',
            ],
            [
                'id'    => 62,
                'title' => 'lesson_show',
            ],
            [
                'id'    => 63,
                'title' => 'lesson_delete',
            ],
            [
                'id'    => 64,
                'title' => 'lesson_access',
            ],
            [
                'id'    => 65,
                'title' => 'video_create',
            ],
            [
                'id'    => 66,
                'title' => 'video_edit',
            ],
            [
                'id'    => 67,
                'title' => 'video_show',
            ],
            [
                'id'    => 68,
                'title' => 'video_delete',
            ],
            [
                'id'    => 69,
                'title' => 'video_access',
            ],
            [
                'id'    => 70,
                'title' => 'comment_create',
            ],
            [
                'id'    => 71,
                'title' => 'comment_edit',
            ],
            [
                'id'    => 72,
                'title' => 'comment_show',
            ],
            [
                'id'    => 73,
                'title' => 'comment_delete',
            ],
            [
                'id'    => 74,
                'title' => 'comment_access',
            ],
            [
                'id'    => 75,
                'title' => 'profile_password_edit',
            ],
        ];

        Permission::insert($permissions);
    }
}
