<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['colocation_id', 'from_user_id', 'to_user_id', 'amount', 'date', 'note'];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function colocation(): BelongsTo
    {
        return $this->belongsTo(Colocation::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
