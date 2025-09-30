<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\ContactList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layout')]
class ListShow extends Component
{
    use WithPagination;

    public ContactList $list;

    public array $selectedContacts = [];
    public string $contactSearch = '';

    protected $listeners = ['listUpdated' => '$refresh'];

    public function mount(ContactList $list): void
    {
        $this->ensureAuthorized($list);
        $this->list = $list;
    }

    public function updatingContactSearch(): void
    {
        $this->resetPage('availableContacts');
    }

    public function addContacts(): void
    {
        if (empty($this->selectedContacts)) {
            return;
        }

        $now = Date::now();

        $payload = collect($this->selectedContacts)
            ->mapWithKeys(fn ($id) => [$id => ['added_at' => $now]])
            ->toArray();

        $this->list->contacts()->syncWithoutDetaching($payload);
        $this->selectedContacts = [];
        $this->reloadList();
        $this->dispatch('listUpdated');
        session()->flash('status', 'Contacts added to the list.');
    }

    public function removeContact(int $contactId): void
    {
        $this->list->contacts()->detach($contactId);
        $this->reloadList();
        session()->flash('status', 'Contact removed from the list.');
    }

    protected function reloadList(): void
    {
        $this->list->refresh();
    }

    public function archive(): void
    {
        $this->list->archive();
        $this->reloadList();
        session()->flash('status', 'List archived.');
    }

    public function restore(): void
    {
        $this->list->restore();
        $this->reloadList();
        session()->flash('status', 'List restored.');
    }

    public function export(): void
    {
        // Placeholder for export implementation.
    }

    public function getListContactsProperty()
    {
        return $this->list->contacts()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }

    public function getAvailableContactsProperty()
    {
        $query = Contact::visibleTo()
            ->whereDoesntHave('lists', fn ($q) => $q->where('lists.id', $this->list->id))
            ->when($this->contactSearch, function ($query) {
                $query->where(function ($inner) {
                    $inner->where('first_name', 'like', "%{$this->contactSearch}%")
                        ->orWhere('last_name', 'like', "%{$this->contactSearch}%")
                        ->orWhere('email', 'like', "%{$this->contactSearch}%");
                });
            })
            ->orderBy('first_name')
            ->orderBy('last_name');

        return $query->paginate(8, pageName: 'availableContacts');
    }

    public function render()
    {
        return view('livewire.list-show', [
            'listContacts' => $this->listContacts,
            'availableContacts' => $this->availableContacts,
        ]);
    }

    protected function ensureAuthorized(ContactList $list): void
    {
        abort_unless($list->user_id === Auth::id(), 403);
    }
}
