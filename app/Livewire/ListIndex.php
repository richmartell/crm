<?php

namespace App\Livewire;

use App\Models\ContactList;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layout')]
class ListIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showArchived = false;

    protected $listeners = ['listUpdated' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleArchived(): void
    {
        $this->showArchived = ! $this->showArchived;
        $this->resetPage();
    }

    public function getListsProperty()
    {
        return ContactList::query()
            ->visibleTo()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->when($this->showArchived, fn ($query) => $query->archived(), fn ($query) => $query->active())
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.list-index', [
            'lists' => $this->lists,
        ]);
    }
}
