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
        Schema::table('price_simulations', function (Blueprint $table): void {
            $table->json('material_price_changes')->nullable();
            $table->decimal('variable_material_cost_per_unit', 15, 4)->nullable();
            $table->decimal('variable_labor_cost_per_unit', 15, 4)->nullable();
            $table->decimal('variable_overhead_cost_per_unit', 15, 4)->nullable();
            $table->decimal('variable_cost_per_unit', 15, 4)->nullable();
            $table->decimal('fixed_cost_per_period', 15, 2)->nullable();
            $table->unsignedBigInteger('break_even_units')->nullable();
            $table->index(['product_id', 'created_at', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_simulations', function (Blueprint $table): void {
            $table->dropIndex(['product_id', 'created_at', 'id']);
            $table->dropColumn([
                'material_price_changes',
                'variable_material_cost_per_unit',
                'variable_labor_cost_per_unit',
                'variable_overhead_cost_per_unit',
                'variable_cost_per_unit',
                'fixed_cost_per_period',
                'break_even_units',
            ]);
        });
    }
};
