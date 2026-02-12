<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_items_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_item_id')->nullable()->constrained('service_items')->nullOnDelete();
            $table->string('code');
            $table->string('description');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('quantity', 15, 4)->default(1);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();

            $table->index('service_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_items_pivot');
    }
};
