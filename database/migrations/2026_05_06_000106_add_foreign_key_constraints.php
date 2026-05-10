<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tblfilter_defaults', function (Blueprint $table) {
            $table->index('filter_id');
            $table->foreign('filter_id')->references('id')->on('tblfilters')->onDelete('cascade');
            $table->index('staff_id');
            $table->foreign('staff_id')->references('staffid')->on('tblstaff')->onDelete('cascade');
        });

        Schema::table('tbltwocheckout_log', function (Blueprint $table) {
            $table->index('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('tblinvoices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tblfilter_defaults', function (Blueprint $table) {
            $table->dropForeign(['filter_id']);
            $table->dropForeign(['staff_id']);
        });

        Schema::table('tbltwocheckout_log', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });
    }
};
