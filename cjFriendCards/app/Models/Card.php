<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'unique_name',
        'first_name',
        'last_name',
        'address',
        'phone',
        'email_work',
        'email_personal',
        'email_extra1',
        'email_extra2',
        'email_extra3',
        'birthday',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date',
    ];

    /**
     * Get the relationships where this card is the primary card.
     */
    public function relationships(): HasMany
    {
        return $this->hasMany(Relationship::class, 'card_id');
    }

    /**
     * Get the relationships where this card is the related card.
     */
    public function relatedRelationships(): HasMany
    {
        return $this->hasMany(Relationship::class, 'related_card_id');
    }

    /**
     * Get all related cards through relationships.
     */
    public function relatedCards()
    {
        return $this->belongsToMany(
            Card::class,
            'relationships',
            'card_id',
            'related_card_id'
        )->withPivot('relationship_type', 'notes');
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Generate vCard content for this card.
     */
    public function toVcard(): string
    {
        $vcard = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";
        $vcard .= "FN:" . $this->full_name . "\r\n";
        $vcard .= "N:" . $this->last_name . ";" . $this->first_name . "\r\n";

        if ($this->phone) {
            $vcard .= "TEL:" . $this->phone . "\r\n";
        }

        if ($this->email_work) {
            $vcard .= "EMAIL;TYPE=WORK:" . $this->email_work . "\r\n";
        }

        if ($this->email_personal) {
            $vcard .= "EMAIL;TYPE=PERSONAL:" . $this->email_personal . "\r\n";
        }

        if ($this->email_extra1) {
            $vcard .= "EMAIL;TYPE=OTHER:" . $this->email_extra1 . "\r\n";
        }

        if ($this->email_extra2) {
            $vcard .= "EMAIL;TYPE=OTHER:" . $this->email_extra2 . "\r\n";
        }

        if ($this->email_extra3) {
            $vcard .= "EMAIL;TYPE=OTHER:" . $this->email_extra3 . "\r\n";
        }

        if ($this->address) {
            $vcard .= "ADR;;" . $this->address . "\r\n";
        }

        if ($this->birthday) {
            $vcard .= "BDAY:" . $this->birthday->format('Y-m-d') . "\r\n";
        }

        if ($this->notes) {
            $vcard .= "NOTE:" . $this->notes . "\r\n";
        }

        $vcard .= "END:VCARD\r\n";

        return $vcard;
    }
}
