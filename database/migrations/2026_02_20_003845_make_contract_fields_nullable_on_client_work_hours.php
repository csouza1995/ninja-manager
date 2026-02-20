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
            $table->string('contract_type')->nullable()->change();
            $table->unsignedSmallInteger('contract_weeks')->nullable()->change();
            $table->unsignedSmallInteger('contract_days')->nullable()->change();
            $table->unsignedSmallInteger('contract_hours_raw')->nullable()->change();
            $table->unsignedSmallInteger('contract_minutes_raw')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_work_hours', function (Blueprint $table) {
            $table->string('contract_type')->nullable(false)->default('fixed')->change();
            $table->unsignedSmallInteger('contract_weeks')->nullable(false)->default(0)->change();
            $table->unsignedSmallInteger('contract_days')->nullable(false)->default(0)->change();
            $table->unsignedSmallInteger('contract_hours_raw')->nullable(false)->default(0)->change();
            $table->unsignedSmallInteger('contract_minutes_raw')->nullable(false)->default(0)->change();
        });
    }
};
