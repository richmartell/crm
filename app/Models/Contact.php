<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ContactList;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'is_shared',
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
        'is_shared' => 'boolean',
    ];

    protected $appends = ['full_name'];

    protected static function booted()
    {
        // Automatically assign the authenticated user when creating a contact
        static::creating(function ($contact) {
            if (Auth::check() && ! $contact->user_id) {
                $contact->user_id = Auth::id();
            }
        });
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Relationship to user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(ContactList::class, 'contact_list')
            ->withPivot(['added_at'])
            ->withTimestamps();
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

    // Scope to filter contacts visible to the current user
    public function scopeVisibleTo($query, ?User $user = null)
    {
        $user = $user ?: auth()->user();
        
        if (! $user) {
            return $query->whereRaw('1 = 0'); // Return empty if no user
        }

        return $query->where(function ($inner) use ($user) {
            $inner->where('user_id', $user->id)  // User's own contacts
              ->orWhere('is_shared', true);    // Or shared contacts
        });
    }

    // Scope for only personal contacts
    public function scopePersonal($query, ?User $user = null)
    {
        $user = $user ?: auth()->user();
        
        return $query->where('user_id', optional($user)->id)
                     ->where('is_shared', false);
    }

    // Scope for only shared contacts
    public function scopeShared($query)
    {
        return $query->where('is_shared', true);
    }
}