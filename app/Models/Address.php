<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    protected $fillable = [
        'street',
        'city',
        'postcode',
        'country',
    ];

    protected $appends = ['formatted_address'];

    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street,
            $this->city,
            $this->postcode,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    // Relationship to contacts
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}