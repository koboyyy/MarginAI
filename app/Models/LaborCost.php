<?php

namespace App\Models;

use App\Enums\LaborRateType;
use Database\Factories\LaborCostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LaborCost extends Model
{
    /** @use HasFactory<LaborCostFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'rate_type',
        'rate',
    ];

    protected function casts(): array
    {
        return [
            'rate_type' => LaborRateType::class,
            'rate' => 'decimal:4',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_labor_costs')
            ->withPivot(['quantity_used', 'subtotal_cost'])
            ->withTimestamps();
    }
}
