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
        Schema::table('outflows', function (Blueprint $table) {
            $table->date('reference_date')->nullable()->after('due_date');
        });

        Schema::table('expenditures', function (Blueprint $table) {
            $table->date('reference_date')->nullable()->after('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outflows', function (Blueprint $table) {
            $table->dropColumn('reference_date');
        });

        Schema::table('expenditures', function (Blueprint $table) {
            $table->dropColumn('reference_date');
        });
    }
};
