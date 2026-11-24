<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'batch_yield',
        'batches_per_month',
        'selling_price',
    ];

    protected function casts(): array
    {
        return [
            'batch_yield' => 'decimal:2',
            'batches_per_month' => 'decimal:2',
            'labor_cost_per_batch' => 'decimal:4',
            'overhead_cost_per_batch' => 'decimal:4',
            'calculated_hpp' => 'decimal:2',
            'recommended_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(RawMaterial::class, 'product_materials')
            ->withPivot(['quantity_used', 'subtotal_cost'])
            ->withTimestamps();
    }

    public function laborCosts(): BelongsToMany
    {
        return $this->belongsToMany(LaborCost::class, 'product_labor_costs')
            ->withPivot(['quantity_used', 'subtotal_cost'])
            ->withTimestamps();
    }

    public function overheadCosts(): BelongsToMany
    {
        return $this->belongsToMany(OverheadCost::class, 'product_overhead_costs')
            ->withPivot('subtotal_cost')
            ->withTimestamps();
    }

    public function priceSimulations(): HasMany
    {
        return $this->hasMany(PriceSimulation::class);
    }
}
