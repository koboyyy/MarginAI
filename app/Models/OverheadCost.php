<?php

namespace App\Models;

use App\Enums\OverheadCostType;
use Database\Factories\OverheadCostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OverheadCost extends Model
{
    /** @use HasFactory<OverheadCostFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'type' => OverheadCostType::class,
            'amount' => 'decimal:4',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_overhead_costs')
            ->withPivot('subtotal_cost')
            ->withTimestamps();
    }
}
