<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('front_desk_items', function (Blueprint $table) {
            if (!Schema::hasColumn('front_desk_items', 'passed_by')) {
                $table->foreignId('passed_by')->nullable()->after('passed_to')->constrained('users')->onDelete('restrict');
            }
            if (!Schema::hasColumn('front_desk_items', 'passed_at')) {
                $table->timestamp('passed_at')->nullable()->after('passed_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('front_desk_items', function (Blueprint $table) {
            $table->dropForeign(['passed_by']);
            $table->dropColumn(['passed_by', 'passed_at']);
        });
    }
};