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
     * Relationship type opposites mapping.
     */
    private static array $opposites = [
        'parent' => 'child',
        'child' => 'parent',
        'spouse' => 'spouse',
        'ex-partner' => 'ex-partner',
        'friend' => 'friend',
        'colleague' => 'colleague',
        'acquaintance' => 'acquaintance',
        'family' => 'family',
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

    /**
     * Get the opposite relationship type.
     */
    public static function getOppositeType(string $type): string
    {
        return self::$opposites[$type] ?? $type;
    }

    /**
     * Check if this relationship type should have an opposite.
     */
    public static function hasOpposite(string $type): bool
    {
        return array_key_exists($type, self::$opposites);
    }
}
