<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentTime extends Model
{
    use HasFactory;

    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'user_id',
        'start_time',
        'end_time',
        'day_of_week',
        'specific_date',
        'duration',
        'type',
        'break_start',
        'break_end',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'break_start' => 'datetime:H:i',
        'break_end' => 'datetime:H:i',
        'specific_date' => 'date',
        'is_active' => 'boolean',
        'duration' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('type', 'available');
    }

    public function scopeBreaks($query)
    {
        return $query->whereIn('type', ['break', 'lunch']);
    }

    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('specific_date', $date);
    }

    public function isBreakTime(): bool
    {
        return in_array($this->type, ['break', 'lunch']);
    }

    public function isAvailable(): bool
    {
        return $this->type === 'available' && $this->is_active;
    }

    public function getFormattedTimeRangeAttribute(): string
    {
        $start = $this->start_time ? $this->start_time->format('H:i') : '';
        $end = $this->end_time ? $this->end_time->format('H:i') : '';
        
        if ($start && $end) {
            return "{$start} - {$end}";
        }
        
        return $start ?: $end;
    }

    public function getFormattedBreakTimeAttribute(): string
    {
        if (!$this->break_start || !$this->break_end) {
            return '';
        }
        
        $start = $this->break_start->format('H:i');
        $end = $this->break_end->format('H:i');
        
        return "{$start} - {$end}";
    }

    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'available' => 'Disponível',
            'break' => 'Intervalo',
            'lunch' => 'Almoço',
            default => $this->type
        };
    }
}