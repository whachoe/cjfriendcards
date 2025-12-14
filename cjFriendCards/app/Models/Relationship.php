<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Relationship extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'card_id',
        'related_card_id',
        'relationship_type',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'relationship_type' => 'string',
    ];

    /**
     * Get the card that owns this relationship.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    /**
     * Get the related card.
     */
    public function relatedCard(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'related_card_id');
    }
}
