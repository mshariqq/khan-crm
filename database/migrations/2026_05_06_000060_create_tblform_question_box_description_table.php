<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblform_question_box_description', function (Blueprint $table) {
            $table->bigIncrements('questionboxdescriptionid');
            $table->longText('description');
            $table->longText('boxid');
            $table->integer('questionid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblform_question_box_description');
    }
};
