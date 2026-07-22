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
        Schema::table('organizer_applications', function (Blueprint $table) {
            $table->string('organization_name')->nullable()->after('user_id');
            $table->string('business_reg_number')->nullable()->after('organization_name');
            $table->string('contact_phone')->nullable()->after('business_reg_number');
            $table->string('bank_name')->nullable()->after('contact_phone');
            $table->string('bank_account_number')->nullable()->after('bank_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizer_applications', function (Blueprint $table) {
            $table->dropColumn([
                'organization_name',
                'business_reg_number',
                'contact_phone',
                'bank_name',
                'bank_account_number',
            ]);
        });
    }
};
