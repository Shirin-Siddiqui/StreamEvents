<?php

// app/Models/Follower.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Subscriber extends Model {

    use HasFactory;

    const TIER1 = 1;
    const TIER2 = 2;
    const TIER3 = 3;

    /**
     * mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'tier_id',
        'user_id'
    ];
    
    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphOne
     */
    public function event(): MorphOne {
        return $this->morphOne(Event::class, 'eventable');
    }

}
