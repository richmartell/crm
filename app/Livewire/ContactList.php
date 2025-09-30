<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\Tag;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layout')]
class ContactList extends Component
{
    public string $search = '';
    public ?int $tagFilter = null;
    public string $sortField = 'last_name';
    public string $sortDirection = 'asc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTagFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $contacts = Contact::query()
            ->with(['tags', 'address'])
            ->visibleTo()
            ->when($this->search, function ($query) {
                $query->where(function ($inner) {
                    $inner->where('first_name', 'like', "%{$this->search}%")
                          ->orWhere('last_name', 'like', "%{$this->search}%")
                          ->orWhere('email', 'like', "%{$this->search}%")
                          ->orWhere('phone_number', 'like', "%{$this->search}%");
                });
            })
            ->when($this->tagFilter, fn ($query) => $query->whereHas('tags', fn ($tagQuery) => $tagQuery->where('tags.id', $this->tagFilter)))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(12);

        return view('livewire.contact-list', [
            'contacts' => $contacts,
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }
}