<?php

use App\Models\PrepareText;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prepare_texts', function (Blueprint $table) {
            $table->id();
            $table->integer('type');
            $table->longText('text');
            $table->softDeletes();
            $table->timestamps();
        });

        $array=[
            [
            'type'=>1,
            'text'=>'In Task 1 in the Multilevel Writing test, you are asked to write a letter, where you demonstrate your ability to communicate using English letter-writing conventions.'
            ],
            [
            'type'=>2,
            'text'=>'Multilevel Writing Task 2 is an essay-writing task that assesses your ability to organize and present ideas effectively in response to a prompt. It accounts for 2/3 of your Writing band score.'
            ],
            [
            'type'=>3,
            'text'=>'In this part l am going to ask you three short questions about yourself and your interests. You will have 30 seconds to reply to each question. If you ready click "Boshlash" button to start exam.'
            ],
            [
            'type'=>4,
            'text'=>'In this part I am going to ask you three short questions about yourself and your interests.You will have 30 seconds to reply to each question. If you ready click "Boshlash" button to start exam.'
            ],
            [
            'type'=>5,
            'text'=>'In this part I am going to ask you three short questions about yourself and your interests. You will have 30 seconds to reply to each question. If you ready click "Boshlash" button to start exam.'
            ]
        ];

        PrepareText::insert($array);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prepare_texts');
    }
};
