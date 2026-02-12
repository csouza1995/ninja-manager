<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description');
            $table->text('internal_description')->nullable();
            $table->decimal('unit_price', 10, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_items');
    }
};
