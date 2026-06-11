<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Borrowing extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['status_label', 'status_color', 'overdue_days'];

    protected $fillable = [
        'user_id',
        'transaction_code',
        'status',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'purpose',
        'notes',
        'rejection_reason',
        'total_fine',
        'fine_paid',
        'approved_by',
        'approved_at',
        'handed_by',
        'handed_at',
        'received_by',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'expected_return_date' => 'date',
            'actual_return_date' => 'date',
            'approved_at' => 'datetime',
            'handed_at' => 'datetime',
            'received_at' => 'datetime',
            'total_fine' => 'decimal:2',
            'fine_paid' => 'boolean',
        ];
    }

    // ── Auto-generate transaction code ───────────────────────

    protected static function booted(): void
    {
        static::creating(function (Borrowing $borrowing) {
            if (empty($borrowing->transaction_code)) {
                $borrowing->transaction_code = self::generateTransactionCode();
            }
        });
    }

    public static function generateTransactionCode(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        return "BRW-{$date}-{$random}";
    }

    // ── Relationships ────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handed_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
                     ->where('expected_return_date', '<', now()->toDateString());
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isOverdue(): bool
    {
        if (!in_array($this->status, ['active', 'overdue'])) {
            return false;
        }

        return $this->expected_return_date->isPast();
    }

    public function getOverdueDaysAttribute(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        $endDate = $this->actual_return_date ?? Carbon::today();

        return (int) $this->expected_return_date->diffInDays($endDate);
    }

    public function calculateTotalFine(): float
    {
        $totalFine = 0;

        foreach ($this->details as $detail) {
            $item = $detail->item;
            $overdueDays = $this->overdue_days;
            $totalFine += ($item->fine_per_day * $overdueDays * $detail->quantity);
            $totalFine += $detail->damage_fee;
        }

        return $totalFine;
    }

    /**
     * Get badge color for status display.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'active' => 'green',
            'partially_returned' => 'indigo',
            'returned' => 'gray',
            'overdue' => 'red',
            'rejected' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'active' => 'Sedang Dipinjam',
            'partially_returned' => 'Dikembalikan Sebagian',
            'returned' => 'Dikembalikan',
            'overdue' => 'Terlambat',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }
}
