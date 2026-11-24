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
            $table->text('recommendation_error')->nullable()->after('recommendation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_simulations', function (Blueprint $table): void {
            $table->dropColumn('recommendation_error');
        });
    }
};
