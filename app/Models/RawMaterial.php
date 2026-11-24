<?php

namespace App\Models;

use Database\Factories\RawMaterialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RawMaterial extends Model
{
    /** @use HasFactory<RawMaterialFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'unit_price',
        'wastage_percent',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:4',
            'wastage_percent' => 'decimal:2',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_materials')
            ->withPivot(['quantity_used', 'subtotal_cost'])
            ->withTimestamps();
    }
}
