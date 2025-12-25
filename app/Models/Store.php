<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'image_logo',
        'image_banner',
        'slug',
        'celphone',
        'zipcode',
        'address',
        'neighborhood',
        'city',
        'number',
        'state',
        'complement',
        'instagram',
        'facebook',
        'whatsapp',
        'email',
        'color_primary',
        'color_secondary',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
