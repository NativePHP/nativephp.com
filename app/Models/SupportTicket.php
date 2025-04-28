<?php

namespace App\Models;

use App\Models\SupportTicket\Reply;
use App\SupportTicket\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    protected $casts = [
        'status' => Status::class,
    ];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            if (is_null($ticket->mask)) {
                // @TODO Generate a unique mask for the ticket
                $ticket->mask = uniqid('ticket_');
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'mask';
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class)
            ->orderBy('created_at', 'desc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
