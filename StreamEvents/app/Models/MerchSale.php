<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class MerchSale extends Model
{
    use HasFactory;

    /**
     * mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'amount',
        'price',
        'price_usd',
        'currency',
        'user_id'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphOne
     */
    public function event(): MorphOne
    {
        return $this->morphOne(Event::class, 'eventable');
    }
}
