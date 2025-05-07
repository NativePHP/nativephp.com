<?php

namespace App\Models\SupportTicket;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'support_ticket_id',
        'message',
        'attachments',
        'note',
    ];

    protected $casts = [
        'attachments' => 'array',
        'note' => 'boolean',
    ];

    public function isFromAdmin(): Attribute
    {
        return Attribute::get(fn () => $this->user->is_admin)
            ->shouldCache();
    }

    public function isFromUser(): Attribute
    {
        return Attribute::get(fn () => $this->user_id === auth()->user()->id)
            ->shouldCache();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supportTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }
}
