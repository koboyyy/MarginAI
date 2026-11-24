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
            $table->decimal('base_recommended_price', 15, 2)->nullable();
            $table->decimal('hpp_snapshot', 15, 2)->nullable();
            $table->decimal('target_margin_percent', 5, 2)->nullable();
            $table->json('competitor_prices')->nullable();
            $table->string('recommendation_status')->default('not_requested');
            $table->decimal('psychological_price', 15, 2)->nullable();
            $table->string('margin_risk')->nullable();
            $table->json('competition_analysis')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_simulations', function (Blueprint $table): void {
            $table->dropColumn([
                'base_recommended_price',
                'hpp_snapshot',
                'target_margin_percent',
                'competitor_prices',
                'recommendation_status',
                'psychological_price',
                'margin_risk',
                'competition_analysis',
            ]);
        });
    }
};
