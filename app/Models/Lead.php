<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Lead extends Model
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'company',
        'description',
        'budget',
        'ip_address',
    ];

    public const BUDGETS = [
        'less_than_5k' => 'Less than $5,000',
        '5k_to_10k' => '$5,000 - $10,000',
        '10k_to_25k' => '$10,000 - $25,000',
        '25k_to_50k' => '$25,000 - $50,000',
        '50k_plus' => '$50,000+',
    ];

    protected function budgetLabel(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            return self::BUDGETS[$this->budget] ?? $this->budget;
        });
    }
}
