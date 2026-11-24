<?php

namespace App\Models;

use App\Enums\BusinessCategory;
use App\Enums\PricingMethod;
use Database\Factories\BusinessFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    /** @use HasFactory<BusinessFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'pricing_method',
        'default_margin_percent',
    ];

    protected $attributes = [
        'pricing_method' => 'margin',
    ];

    protected function casts(): array
    {
        return [
            'category' => BusinessCategory::class,
            'pricing_method' => PricingMethod::class,
            'default_margin_percent' => 'decimal:2',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rawMaterials(): HasMany
    {
        return $this->hasMany(RawMaterial::class);
    }

    public function laborCosts(): HasMany
    {
        return $this->hasMany(LaborCost::class);
    }

    public function overheadCosts(): HasMany
    {
        return $this->hasMany(OverheadCost::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
