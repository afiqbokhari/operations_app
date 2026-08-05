<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FrontDeskContact;
use App\Models\FrontDeskItem;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add contact_id and migrate data from received_from
        if (Schema::hasColumn('front_desk_items', 'received_from') && !Schema::hasColumn('front_desk_items', 'contact_id')) {
            Schema::table('front_desk_items', function (Blueprint $table) {
                $table->foreignId('contact_id')->nullable()->after('batch_name');
            });

            $items = FrontDeskItem::whereNotNull('received_from')->get();
            foreach ($items as $item) {
                $contact = FrontDeskContact::firstOrCreate(['name' => $item->received_from]);
                $item->update(['contact_id' => $contact->id]);
            }

            Schema::table('front_desk_items', function (Blueprint $table) {
                $table->foreign('contact_id')->references('id')->on('front_desk_contacts')->onDelete('restrict');
                $table->dropColumn('received_from');
            });
        }

        // Step 2: Add case_reference and passed_to
        if (!Schema::hasColumn('front_desk_items', 'case_reference')) {
            Schema::table('front_desk_items', function (Blueprint $table) {
                $table->string('case_reference', 20)->nullable()->after('matter_id');
            });
        }
        if (!Schema::hasColumn('front_desk_items', 'passed_to')) {
            Schema::table('front_desk_items', function (Blueprint $table) {
                $table->foreignId('passed_to')->nullable()->after('address_to')->constrained('users')->onDelete('restrict');
            });
        }

        // Step 3: Change batch_name to batch_number
        if (Schema::hasColumn('front_desk_items', 'batch_name') && !Schema::hasColumn('front_desk_items', 'batch_number')) {
            Schema::table('front_desk_items', function (Blueprint $table) {
                $table->dropColumn('batch_name');
                $table->tinyInteger('batch_number')->nullable()->after('date_received');
            });
        }
    }

    public function down(): void
    {
        // Reverse batch change
        if (Schema::hasColumn('front_desk_items', 'batch_number') && !Schema::hasColumn('front_desk_items', 'batch_name')) {
            Schema::table('front_desk_items', function (Blueprint $table) {
                $table->dropColumn('batch_number');
                $table->string('batch_name')->nullable()->after('date_received');
            });
        }

        // Reverse passed_to and case_reference
        Schema::table('front_desk_items', function (Blueprint $table) {
            if (Schema::hasColumn('front_desk_items', 'passed_to')) {
                $table->dropForeign(['passed_to']);
                $table->dropColumn('passed_to');
            }
            if (Schema::hasColumn('front_desk_items', 'case_reference')) {
                $table->dropColumn('case_reference');
            }
        });

        // Reverse contact_id
        if (Schema::hasColumn('front_desk_items', 'contact_id')) {
            Schema::table('front_desk_items', function (Blueprint $table) {
                $table->string('received_from')->nullable()->after('batch_name');
            });

            $items = FrontDeskItem::whereNotNull('contact_id')->get();
            foreach ($items as $item) {
                $item->update(['received_from' => $item->contact?->name]);
            }

            Schema::table('front_desk_items', function (Blueprint $table) {
                $table->dropForeign(['contact_id']);
                $table->dropColumn('contact_id');
            });
        }
    }
};