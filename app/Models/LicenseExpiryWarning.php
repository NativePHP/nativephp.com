<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseExpiryWarning extends Model
{
    protected $fillable = [
        'license_id',
        'warning_days',
        'sent_at',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }
}
