<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'mask',
        'subject',
        'message',
        'status',
    ];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            if (is_null($ticket->mask)) {

            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'mask';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
