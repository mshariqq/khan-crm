<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblestimate_request_forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('form_key', 32);
            $table->string('type', 100);
            $table->string('name', 191);
            $table->longText('form_data')->nullable();
            $table->integer('recaptcha')->nullable();
            $table->integer('status');
            $table->string('submit_btn_name', 100)->nullable();
            $table->string('submit_btn_bg_color', 10)->default('#84c529');
            $table->string('submit_btn_text_color', 10)->default('#ffffff');
            $table->mediumText('success_submit_msg')->nullable();
            $table->integer('submit_action')->default(0);
            $table->longText('submit_redirect_url')->nullable();
            $table->string('language', 100)->nullable();
            $table->dateTime('dateadded')->nullable();
            $table->string('notify_type', 100)->nullable();
            $table->longText('notify_ids')->nullable();
            $table->integer('responsible')->nullable();
            $table->integer('notify_request_submitted')->default(0);
        $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblestimate_request_forms');
    }
};
