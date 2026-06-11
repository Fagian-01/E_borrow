<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'specifications',
        'image',
        'total_stock',
        'available_stock',
        'max_borrow_days',
        'max_qty_per_user',
        'fine_per_day',
        'is_active',
        'requires_approval',
    ];

    protected function casts(): array
    {
        return [
            'total_stock' => 'integer',
            'available_stock' => 'integer',
            'max_borrow_days' => 'integer',
            'max_qty_per_user' => 'integer',
            'fine_per_day' => 'decimal:2',
            'is_active' => 'boolean',
            'requires_approval' => 'boolean',
        ];
    }

    // ── Auto-generate slug ───────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Item $item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->name);
            }
        });
    }

    // ── Relationships ────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function borrowingDetails(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)->where('available_stock', '>', 0);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isAvailable(): bool
    {
        return $this->is_active && $this->available_stock > 0;
    }

    public function hasStock(int $quantity = 1): bool
    {
        return $this->available_stock >= $quantity;
    }

    /**
     * Get image URL with fallback.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return asset('images/placeholder-item.png');
    }
}
