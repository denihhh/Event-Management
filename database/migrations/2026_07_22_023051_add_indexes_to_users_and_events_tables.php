<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->index('is_organizer');
            $table->index('is_admin');
        });

        Schema::table('events', function (Blueprint $table): void {
            $table->index(['starts_at', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['is_organizer']);
            $table->dropIndex(['is_admin']);
        });

        Schema::table('events', function (Blueprint $table): void {
            $table->dropIndex(['starts_at', 'deleted_at']);
        });
    }
};
