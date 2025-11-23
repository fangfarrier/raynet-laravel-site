<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the sort_order column to the event_types table.
     *
     * The controllers expect to be able to do:
     *   EventType::orderBy('sort_order')->orderBy('name')
     *
     * So we add an unsigned integer with a sensible default.
     */
    public function up(): void
    {
        Schema::table('event_types', function (Blueprint $table) {
            // Only add if it doesn't already exist (safe if we re-run migrations on another env)
            if (! Schema::hasColumn('event_types', 'sort_order')) {
                $table->unsignedInteger('sort_order')
                      ->default(0)
                      ->after('name'); // keep it next to the name column for sanity
            }
        });
    }

    /**
     * Rollback: drop the sort_order column.
     * Not strictly needed day-to-day, but it keeps the migration reversible.
     */
    public function down(): void
    {
        Schema::table('event_types', function (Blueprint $table) {
            if (Schema::hasColumn('event_types', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};