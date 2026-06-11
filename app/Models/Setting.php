<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    // ── Static Helpers ───────────────────────────────────────

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        $storeValue = is_array($value) ? json_encode($value) : (string) $value;

        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $storeValue,
                'group' => $group,
                'type' => $type,
            ],
        );
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
