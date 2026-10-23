<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToSavedLessonsTable extends Migration
{
    public function up()
    {
        Schema::table('saved_lessons', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'user_fk_9851885')->references('id')->on('users');
            $table->unsignedBigInteger('video_id')->nullable();
            $table->foreign('video_id', 'video_fk_9852440')->references('id')->on('videos');
        });
    }
}
