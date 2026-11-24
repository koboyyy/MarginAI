<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceSimulation extends Model
{
    protected $fillable = [
        'simulated_price',
        'estimated_units_sold',
        'projected_revenue',
        'projected_profit',
        'base_recommended_price',
        'hpp_snapshot',
        'target_margin_percent',
        'competitor_prices',
        'recommendation_status',
        'recommendation_error',
        'psychological_price',
        'ai_notes',
        'margin_risk',
        'competition_analysis',
        'material_price_changes',
        'variable_material_cost_per_unit',
        'variable_labor_cost_per_unit',
        'variable_overhead_cost_per_unit',
        'variable_cost_per_unit',
        'fixed_cost_per_period',
        'break_even_units',
    ];

    protected function casts(): array
    {
        return [
            'simulated_price' => 'decimal:2',
            'estimated_units_sold' => 'integer',
            'projected_revenue' => 'decimal:2',
            'projected_profit' => 'decimal:2',
            'base_recommended_price' => 'decimal:2',
            'hpp_snapshot' => 'decimal:2',
            'target_margin_percent' => 'decimal:2',
            'competitor_prices' => 'array',
            'recommendation_status' => 'string',
            'recommendation_error' => 'string',
            'psychological_price' => 'decimal:2',
            'margin_risk' => 'string',
            'competition_analysis' => 'array',
            'material_price_changes' => 'array',
            'variable_material_cost_per_unit' => 'decimal:4',
            'variable_labor_cost_per_unit' => 'decimal:4',
            'variable_overhead_cost_per_unit' => 'decimal:4',
            'variable_cost_per_unit' => 'decimal:4',
            'fixed_cost_per_period' => 'decimal:2',
            'break_even_units' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
