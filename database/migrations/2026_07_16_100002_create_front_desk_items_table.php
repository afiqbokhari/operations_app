<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('front_desk_items', function (Blueprint $table) {
            $table->id();
            $table->date('date_received');
            $table->string('batch_name')->nullable();
            $table->string('received_from');
            $table->string('address_to');
            $table->date('letter_date')->nullable();
            $table->foreignId('matter_id')->nullable()->constrained('front_desk_matters')->onDelete('restrict');
            $table->enum('received_via', ['Hand Delivery', 'Courier', 'Post']);
            $table->json('doc_type');
            $table->text('details')->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamp('collected_at')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('logged_by')->constrained('users')->onDelete('restrict');
            $table->softDeletes();
            $table->timestamps();

            $table->index('date_received');
            $table->index('batch_name');
            $table->index('matter_id');
            $table->index('collected_by');
            $table->index('received_via');
            $table->index('logged_by');
            $table->index(['date_received', 'collected_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('front_desk_items');
    }
};