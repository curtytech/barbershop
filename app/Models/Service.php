<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'price',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }

    /**
     * Get the appointments for the service.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get upcoming appointments for the service.
     */
    public function upcomingAppointments()
    {
        return $this->hasMany(Appointment::class)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('appointment_time');
    }

    /**
     * Get the total number of appointments for this service.
     */
    public function getTotalAppointmentsAttribute(): int
    {
        return $this->appointments()->count();
    }

    /**
     * Get the total revenue from this service.
     */
    public function getTotalRevenueAttribute(): float
    {
        return $this->appointments()->count() * $this->price;
    }

    /**
     * Get formatted total revenue.
     */
    public function getFormattedTotalRevenueAttribute(): string
    {
        return 'R$ ' . number_format($this->total_revenue, 2, ',', '.');
    }
}