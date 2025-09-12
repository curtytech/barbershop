<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'password',
        'role',
        'image_logo',
        'image_banner',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the appointment times for the user.
     */
    public function appointmentTimes()
    {
        return $this->hasMany(AppointmentTime::class);
    }

    /**
     * Get active appointment times for the user.
     */
    public function activeAppointmentTimes()
    {
        return $this->hasMany(AppointmentTime::class)->where('is_active', true);
    }

    /**
     * Get available appointment times for the user.
     */
    public function availableAppointmentTimes()
    {
        return $this->hasMany(AppointmentTime::class)
            ->where('type', 'available')
            ->where('is_active', true);
    }

    /**
     * Get break times for the user.
     */
    public function breakTimes()
    {
        return $this->hasMany(AppointmentTime::class)
            ->whereIn('type', ['break', 'lunch'])
            ->where('is_active', true);
    }
}
