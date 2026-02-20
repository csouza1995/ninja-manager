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
        Schema::table('client_work_hours', function (Blueprint $table) {
            $table->string('contract_type')->default('fixed')->after('mode');
            $table->unsignedSmallInteger('contract_weeks')->default(0)->after('minutes');
            $table->unsignedSmallInteger('contract_days')->default(0)->after('contract_weeks');
            $table->unsignedSmallInteger('contract_hours_raw')->default(0)->after('contract_days');
            $table->unsignedSmallInteger('contract_minutes_raw')->default(0)->after('contract_hours_raw');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_work_hours', function (Blueprint $table) {
            $table->dropColumn(['contract_type', 'contract_weeks', 'contract_days', 'contract_hours_raw', 'contract_minutes_raw']);
        });
    }
};
