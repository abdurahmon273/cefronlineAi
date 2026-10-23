<?php

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
        Schema::create('speaking_prompts', function (Blueprint $table) {
            $table->id();
            $table->integer('type');
            $table->longText('input_prompt_text');
            $table->longText('output_prompt_text');
            $table->timestamps();
        });

        DB::table('speaking_prompts')->insert([
            [
                'type' => 1,
                'input_prompt_text' => 'test part 1.1',
                'output_prompt_text' => 'test part 1.1',
            ],
            [
                'type' => 2,
                'input_prompt_text' => 'test part 1.2',
                'output_prompt_text' => 'test part 1.2',
            ],
            [
                'type' => 3,
                'input_prompt_text' => 'test part 2',
                'output_prompt_text' => 'test part 2',
            ],
            [
                'type' => 4,
                'input_prompt_text' => 'test part 3',
                'output_prompt_text' => 'test part 3',
            ],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speaking_prompts');
    }
};
