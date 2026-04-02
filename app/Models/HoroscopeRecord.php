<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoroscopeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'birth_date',
        'birth_time',
        'gender',
        'timezone',
        'result',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'result' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
