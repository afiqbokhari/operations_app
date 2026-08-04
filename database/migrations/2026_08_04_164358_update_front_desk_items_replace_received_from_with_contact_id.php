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
        // First, make contact_id nullable temporarily if it isn't already
        Schema::table('front_desk_items', function (Blueprint $table) {
            $table->foreignId('contact_id')->nullable()->change();
        });

        // Migrate existing data from received_from (if column still exists)
        if (Schema::hasColumn('front_desk_items', 'received_from')) {
            $items = FrontDeskItem::whereNotNull('received_from')->get();
            foreach ($items as $item) {
                $contact = FrontDeskContact::firstOrCreate(['name' => $item->received_from]);
                $item->update(['contact_id' => $contact->id]);
            }
        }

        // Add foreign key constraint
        Schema::table('front_desk_items', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('front_desk_contacts')->onDelete('restrict');
        });

        // Drop received_from if it exists
        if (Schema::hasColumn('front_desk_items', 'received_from')) {
            Schema::table('front_desk_items', function (Blueprint $table) {
                $table->dropColumn('received_from');
            });
        }
    }

    public function down(): void
    {
        // Add received_from back
        Schema::table('front_desk_items', function (Blueprint $table) {
            $table->string('received_from')->nullable()->after('batch_name');
        });

        // Restore data
        $items = FrontDeskItem::whereNotNull('contact_id')->get();
        foreach ($items as $item) {
            $item->update(['received_from' => $item->contact?->name]);
        }

        // Drop foreign key and column
        Schema::table('front_desk_items', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
            $table->dropColumn('contact_id');
        });
    }
};