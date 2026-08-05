<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('front_desk_items', function (Blueprint $table) {
            $table->string('case_reference', 20)->nullable()->after('matter_id');
            $table->foreignId('passed_to')->nullable()->after('address_to')->constrained('users')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('front_desk_items', function (Blueprint $table) {
            $table->dropForeign(['passed_to']);
            $table->dropColumn(['case_reference', 'passed_to']);
        });
    }
};