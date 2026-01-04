<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'name',
        'email',
        'position',
        'description',
        'phone',
        'image_logo',
        'image_banner',
        'slug',
        'celphone',
        'instagram',
        'facebook',
        'whatsapp',
    ];

    // ✅ RELAÇÃO QUE O FILAMENT PRECISA
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
