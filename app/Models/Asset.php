<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_id',
        'asset_code',
        'barcode',
        'serial_number',
        'status',
        'condition',
        'condition_note',
        'purchase_date',
        'purchase_price',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'purchase_price' => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────────────

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function borrowingDetails(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function markAsBorrowed(): void
    {
        $this->update(['status' => 'borrowed']);
    }

    public function markAsAvailable(): void
    {
        $this->update(['status' => 'available']);
    }

    public function markAsMaintenance(string $note = ''): void
    {
        $this->update([
            'status' => 'maintenance',
            'condition_note' => $note,
        ]);
    }

    /**
     * Get badge color based on status.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available' => 'green',
            'borrowed' => 'blue',
            'maintenance' => 'yellow',
            'damaged' => 'red',
            'lost' => 'gray',
            'retired' => 'gray',
            default => 'gray',
        };
    }
}
