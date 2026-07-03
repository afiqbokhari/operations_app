<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number');
            $table->enum('status', ['active', 'settled', 'adjourned', 'closed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
