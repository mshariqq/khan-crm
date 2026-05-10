<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblcustomfieldsvalues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('relid');
            $table->integer('fieldid');
            $table->string('fieldto', 15);
            $table->mediumText('value');
        $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblcustomfieldsvalues');
    }
};
