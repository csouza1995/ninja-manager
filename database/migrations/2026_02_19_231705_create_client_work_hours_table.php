<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_work_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('type'); // WorkHourType enum: executed | paid
            $table->string('mode'); // WorkHourMode enum: hhh_mm | wdhm
            $table->unsignedInteger('contract_minutes')->nullable(); // total contracted hours in minutes
            $table->unsignedInteger('executed_minutes');             // hours logged in minutes
            // Raw WDHM components (stored for display/editing)
            $table->unsignedSmallInteger('weeks')->default(0);
            $table->unsignedSmallInteger('days')->default(0);
            $table->unsignedSmallInteger('hours')->default(0);
            $table->unsignedSmallInteger('minutes')->default(0);
            // Base configuration per record (can vary per client/period)
            $table->unsignedSmallInteger('base_d')->default(8); // hours per day
            $table->unsignedSmallInteger('base_w')->default(5); // days per week
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_work_hours');
    }
};
