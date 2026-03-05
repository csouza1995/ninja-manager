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
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('description')->nullable()->after('notes');
            $table->foreignId('client_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('service_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            $table->string('ctn')->nullable()->after('access_key');
            $table->string('ctm')->nullable()->after('ctn');
            $table->dateTime('issued_at')->change();
            $table->date('competence_date')->nullable()->after('issued_at');
            $table->decimal('tax_rate', 5, 2)->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['service_id']);
            $table->dropColumn([
                'description',
                'client_id',
                'service_id',
                'ctn',
                'ctm',
                'competence_date',
                'tax_rate',
            ]);
            $table->date('issued_at')->change();
        });
    }
};
