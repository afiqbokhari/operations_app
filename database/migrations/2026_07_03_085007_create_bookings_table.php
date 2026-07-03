<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id')->unique();
            $table->foreignId('case_id')->constrained()->onDelete('restrict');
            $table->foreignId('room_id')->constrained()->onDelete('restrict');
            $table->date('booking_date');
            $table->enum('session_type', ['full_day', 'half_am', 'half_pm', 'overtime']);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('booking_type', ['external', 'internal'])->default('external');
            $table->integer('number_of_attendees')->nullable();
            $table->enum('booking_status', ['tentative', 'confirmed', 'rescheduled', 'in_progress', 'completed', 'no_show', 'cancelled'])->default('confirmed');
            $table->enum('billing_status', ['pending', 'quotation', 'invoice', 'receipt', 'credit_note', 'waived'])->default('pending');
            $table->text('special_requirements')->nullable();
            $table->text('internal_notes')->nullable();
            $table->foreignId('booked_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            
            $table->index('booking_date');
            $table->index(['room_id', 'booking_date']);
            $table->index('case_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
