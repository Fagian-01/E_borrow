<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrowing_id',
        'item_id',
        'asset_id',
        'quantity',
        'condition_out',
        'condition_in',
        'condition_out_note',
        'condition_in_note',
        'photo_out_path',
        'photo_in_path',
        'is_returned',
        'returned_at',
        'returned_to',
        'is_damaged',
        'fine_amount',
        'damage_fee',
        'damage_description',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'is_returned' => 'boolean',
            'is_damaged' => 'boolean',
            'returned_at' => 'datetime',
            'fine_amount' => 'decimal:2',
            'damage_fee' => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────────────

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function returnReceiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeReturned($query)
    {
        return $query->where('is_returned', true);
    }

    public function scopeNotReturned($query)
    {
        return $query->where('is_returned', false);
    }

    public function scopeDamaged($query)
    {
        return $query->where('is_damaged', true);
    }
}
