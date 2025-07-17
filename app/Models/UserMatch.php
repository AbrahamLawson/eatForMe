<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserMatch extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'matched_user_id',
        'availability_id',
        'status',
        'matched_at',
        'response_at',
        'meeting_at',
        'restaurant_name',
        'restaurant_address',
        'restaurant_latitude',
        'restaurant_longitude',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'matched_at' => 'datetime',
        'response_at' => 'datetime',
        'meeting_at' => 'datetime',
        'restaurant_latitude' => 'decimal:7',
        'restaurant_longitude' => 'decimal:7',
    ];
    
    /**
     * Get the user that initiated the match.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the user that was matched with.
     *
     * @return BelongsTo
     */
    public function matchedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'matched_user_id');
    }
    
    /**
     * Get the availability that this match is for.
     *
     * @return BelongsTo
     */
    public function availability(): BelongsTo
    {
        return $this->belongsTo(Availability::class);
    }
    
    /**
     * Get the messages for this match.
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
    
    /**
     * Get the ratings for this match.
     *
     * @return HasMany
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
