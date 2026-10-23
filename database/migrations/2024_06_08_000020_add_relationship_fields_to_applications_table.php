<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToApplicationsTable extends Migration
{
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'user_fk_9851811')->references('id')->on('users');
            $table->unsignedBigInteger('news_id')->nullable();
            $table->foreign('news_id', 'news_fk_9851812')->references('id')->on('newss');
        });
    }
}
