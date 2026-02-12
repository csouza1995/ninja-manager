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
        Schema::table('revenues', function (Blueprint $table) {
            $table->dropColumn('nf_id');
            $table->foreignId('invoice_id')->nullable()->after('gross_amount')->constrained()->nullOnDelete();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('revenues', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invoice_id');
            $table->string('nf_id')->nullable()->after('gross_amount');
        });
    }
};
