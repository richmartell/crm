<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\Tag;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layout')]
class ContactList extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedTag = null;
    public $sortBy = 'first_name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedTag' => ['except' => null],
        'sortBy' => ['except' => 'first_name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedTag()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedTag = null;
        $this->resetPage();
    }

    public function deleteContact($contactId)
    {
        Contact::findOrFail($contactId)->delete();
        session()->flash('success', 'Contact deleted successfully.');
    }

    public function render()
    {
        $query = Contact::query()->with(['address', 'tags']);

        if ($this->search) {
            $query->search($this->search);
        }

        if ($this->selectedTag) {
            $query->withTag($this->selectedTag);
        }

        $contacts = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);

        $tags = Tag::all();

        return view('livewire.contact-list', [
            'contacts' => $contacts,
            'tags' => $tags,
        ]);
    }
}