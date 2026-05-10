<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblform_results', function (Blueprint $table) {
            $table->bigIncrements('resultid');
            $table->integer('boxid');
            $table->integer('boxdescriptionid')->nullable();
            $table->integer('rel_id');
            $table->string('rel_type', 20)->nullable();
            $table->integer('questionid');
            $table->mediumText('answer')->nullable();
            $table->integer('resultsetid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblform_results');
    }
};
