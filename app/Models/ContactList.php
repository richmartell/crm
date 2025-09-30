<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class ContactList extends Model
{
    use HasFactory;

    protected $table = 'lists';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_list', 'list_id', 'contact_id')
            ->withPivot(['added_at'])
            ->withTimestamps();
    }

    public function scopeVisibleTo($query, User $user = null)
    {
        $user = $user ?: auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('user_id', $user->id);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function archive(): void
    {
        $this->update(['archived_at' => Carbon::now()]);
    }

    public function restore(): void
    {
        $this->update(['archived_at' => null]);
    }
}
