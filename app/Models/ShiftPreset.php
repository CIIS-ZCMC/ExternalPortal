<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftPreset extends Model
{
    protected $connection = 'external_employees';
    protected $table = 'shift_presets';

    protected $fillable = [
        'name',
        'code',
        'is_shifting',
        'first_in',
        'first_out',
        'second_in',
        'second_out',
        'total_hours',
        'badge_label',
        'color',
        'border_color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_shifting' => 'boolean',
        'is_active'   => 'boolean',
        'sort_order'  => 'integer',
    ];

    /**
     * Scope active presets ordered by sort_order
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
