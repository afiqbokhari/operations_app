<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('front_desk_items', function (Blueprint $table) {
            $table->dropColumn('batch_name');
            $table->tinyInteger('batch_number')->nullable()->after('date_received');
        });
    }

    public function down(): void
    {
        Schema::table('front_desk_items', function (Blueprint $table) {
            $table->dropColumn('batch_number');
            $table->string('batch_name')->nullable()->after('date_received');
        });
    }
};