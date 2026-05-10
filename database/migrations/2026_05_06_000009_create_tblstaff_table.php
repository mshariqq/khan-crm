<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblstaff', function (Blueprint $table) {
            $table->bigIncrements('staffid');
            $table->string('email', 100);
            $table->string('firstname', 50);
            $table->string('lastname', 50);
            $table->longText('facebook')->nullable();
            $table->longText('linkedin')->nullable();
            $table->string('phonenumber', 30)->nullable();
            $table->string('skype', 50)->nullable();
            $table->string('password', 250);
            $table->dateTime('datecreated');
            $table->string('profile_image', 191)->nullable();
            $table->string('last_ip', 40)->nullable();
            $table->dateTime('last_login')->nullable();
            $table->dateTime('last_activity')->nullable();
            $table->dateTime('last_password_change')->nullable();
            $table->string('new_pass_key', 32)->nullable();
            $table->dateTime('new_pass_key_requested')->nullable();
            $table->integer('admin')->default(0);
            $table->integer('role')->nullable();
            $table->integer('active')->default(1);
            $table->string('default_language', 40)->nullable();
            $table->string('direction', 3)->nullable();
            $table->string('media_path_slug', 191)->nullable();
            $table->integer('is_not_staff')->default(0);
            $table->decimal('hourly_rate', 15, 2)->default(0.00);
            $table->boolean('two_factor_auth_enabled')->default(false);
            $table->string('two_factor_auth_code', 100)->nullable();
            $table->dateTime('two_factor_auth_code_requested')->nullable();
            $table->mediumText('email_signature')->nullable();
            $table->mediumText('google_auth_secret')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblstaff');
    }
};
