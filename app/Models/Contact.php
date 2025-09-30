<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'anniversary_date',
        'email',
        'phone_number',
        'notes',
        'photo',
        'address_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'anniversary_date' => 'date',
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Relationship to address (many contacts can share one address)
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    // Relationship to tags
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    // Relationships to other contacts
    public function relationships(): HasMany
    {
        return $this->hasMany(ContactRelationship::class, 'contact_id');
    }

    // Inverse relationships (where this contact is the related contact)
    public function inverseRelationships(): HasMany
    {
        return $this->hasMany(ContactRelationship::class, 'related_contact_id');
    }

    // Get all related contacts with their relationship types
    public function relatedContacts(): BelongsToMany
    {
        return $this->belongsToMany(
            Contact::class,
            'contact_relationships',
            'contact_id',
            'related_contact_id'
        )->withPivot('relationship_type')->withTimestamps();
    }

    // Scope for searching contacts
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone_number', 'like', "%{$search}%");
        });
    }

    // Scope for filtering by tag
    public function scopeWithTag($query, $tagId)
    {
        return $query->whereHas('tags', function ($q) use ($tagId) {
            $q->where('tags.id', $tagId);
        });
    }
}