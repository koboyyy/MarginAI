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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku', 100)->nullable();
            $table->decimal('batch_yield', 10, 2);
            $table->decimal('labor_cost_per_batch', 15, 2)->default(0);
            $table->decimal('overhead_cost_per_batch', 15, 2)->default(0);
            $table->decimal('calculated_hpp', 15, 2)->default(0);
            $table->decimal('recommended_price', 15, 2)->nullable();
            $table->decimal('selling_price', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
