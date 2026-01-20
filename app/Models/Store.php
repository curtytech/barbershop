<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'email',
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
        'color_primary',
        'color_secondary',
        'image_logo',
        'image_banner',
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
