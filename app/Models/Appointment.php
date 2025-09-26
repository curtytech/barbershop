<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'client_name',
        'client_phone',
        'appointment_time',
        'date',
        'status',
        'notes',
    ];

    protected $casts = [
        'appointment_time' => 'datetime:H:i',
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('date', '<', now()->toDateString());
    }

    public function scopeToday($query)
    {
        return $query->where('date', now()->toDateString());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [
            now()->startOfWeek()->toDateString(),
            now()->endOfWeek()->toDateString()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('date', [
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        ]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function getFormattedDateTimeAttribute(): string
    {
        $date = $this->date->format('d/m/Y');
        $time = $this->appointment_time->format('H:i');
        
        return "{$date} às {$time}";
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d/m/Y');
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->appointment_time->format('H:i');
    }

    public function getDayOfWeekAttribute(): string
    {
        $days = [
            'Sunday' => 'Domingo',
            'Monday' => 'Segunda-feira',
            'Tuesday' => 'Terça-feira',
            'Wednesday' => 'Quarta-feira',
            'Thursday' => 'Quinta-feira',
            'Friday' => 'Sexta-feira',
            'Saturday' => 'Sábado',
        ];
        
        return $days[$this->date->format('l')] ?? $this->date->format('l');
    }

    public function getStatusAttribute(): string
    {
        $now = now();
        $appointmentDateTime = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->appointment_time->format('H:i:s'));
        
        if ($appointmentDateTime->isPast()) {
            return 'Concluído';
        } elseif ($appointmentDateTime->isToday() && $appointmentDateTime->diffInHours($now) <= 2) {
            return 'Próximo';
        } else {
            return 'Agendado';
        }
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'warning',
            'confirmed' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'gray'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'Agendado',
            'confirmed' => 'Confirmado',
            'completed' => 'Concluído',
            'cancelled' => 'Cancelado',
            default => 'Desconhecido'
        };
    }

    public function isPast(): bool
    {
        $appointmentDateTime = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->appointment_time->format('H:i:s'));
        return $appointmentDateTime->isPast();
    }

    public function isToday(): bool
    {
        return $this->date->isToday();
    }

    public function isUpcoming(): bool
    {
        $appointmentDateTime = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->appointment_time->format('H:i:s'));
        return $appointmentDateTime->isFuture();
    }

    public function canBeCancelled(): bool
    {
        $appointmentDateTime = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->appointment_time->format('H:i:s'));
        // Permite cancelamento até 2 horas antes do agendamento
        return $appointmentDateTime->diffInHours(now()) >= 2;
    }
}