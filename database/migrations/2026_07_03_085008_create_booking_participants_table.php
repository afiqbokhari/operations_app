<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->constrained()->onDelete('restrict');
            $table->enum('role', ['presiding_arbitrator', 'co_arbitrator', 'claimant', 'respondent', 'claimant_solicitor', 'respondent_solicitor', 'witness', 'observer']);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->unique(['booking_id', 'contact_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_participants');
    }
};
