<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invitation extends Model
{
    protected $fillable = ['colocation_id', 'email', 'token', 'status', 'accepted_at', 'refused_at'];

    protected $casts = [
        'accepted_at' => 'datetime',
        'refused_at' => 'datetime',
    ];

    public function colocation(): BelongsTo
    {
        return $this->belongsTo(Colocation::class);
    }

    public static function generateToken(): string
    {
        return Str::random(32);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function accept(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function refuse(): void
    {
        $this->update([
            'status' => 'refused',
            'refused_at' => now(),
        ]);
    }
}
