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
        Schema::table('businesses', function (Blueprint $table): void {
            $table->unique('user_id');
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->decimal('batches_per_month', 10, 2)->default(1)->after('batch_yield');
            $table->decimal('labor_cost_per_batch', 15, 4)->default(0)->change();
            $table->decimal('overhead_cost_per_batch', 15, 4)->default(0)->change();
        });

        Schema::table('product_materials', function (Blueprint $table): void {
            $table->decimal('subtotal_cost', 15, 4)->default(0)->change();
        });

        Schema::table('labor_costs', function (Blueprint $table): void {
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('rate_type', 30);
            $table->decimal('rate', 15, 4);
        });

        Schema::table('overhead_costs', function (Blueprint $table): void {
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type', 30);
            $table->decimal('amount', 15, 4);
        });

        Schema::table('product_labor_costs', function (Blueprint $table): void {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('labor_cost_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_used', 12, 4);
            $table->decimal('subtotal_cost', 15, 4)->default(0);
            $table->unique(['product_id', 'labor_cost_id']);
        });

        Schema::table('product_overhead_costs', function (Blueprint $table): void {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('overhead_cost_id')->constrained()->cascadeOnDelete();
            $table->decimal('subtotal_cost', 15, 4)->default(0);
            $table->unique(['product_id', 'overhead_cost_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_overhead_costs', function (Blueprint $table): void {
            $table->dropUnique(['product_id', 'overhead_cost_id']);
            $table->dropConstrainedForeignId('overhead_cost_id');
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn('subtotal_cost');
        });

        Schema::table('product_labor_costs', function (Blueprint $table): void {
            $table->dropUnique(['product_id', 'labor_cost_id']);
            $table->dropConstrainedForeignId('labor_cost_id');
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn(['quantity_used', 'subtotal_cost']);
        });

        Schema::table('overhead_costs', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('business_id');
            $table->dropColumn(['name', 'type', 'amount']);
        });

        Schema::table('labor_costs', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('business_id');
            $table->dropColumn(['name', 'rate_type', 'rate']);
        });

        Schema::table('product_materials', function (Blueprint $table): void {
            $table->decimal('subtotal_cost', 15, 2)->default(0)->change();
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn('batches_per_month');
            $table->decimal('labor_cost_per_batch', 15, 2)->default(0)->change();
            $table->decimal('overhead_cost_per_batch', 15, 2)->default(0)->change();
        });

        Schema::table('businesses', function (Blueprint $table): void {
            $table->dropUnique(['user_id']);
        });
    }
};
