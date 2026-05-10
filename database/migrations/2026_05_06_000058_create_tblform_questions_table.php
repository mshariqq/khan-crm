<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblform_questions', function (Blueprint $table) {
            $table->bigIncrements('questionid');
            $table->integer('rel_id');
            $table->string('rel_type', 20)->nullable();
            $table->longText('question');
            $table->boolean('required')->default(false);
            $table->integer('question_order');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblform_questions');
    }
};
