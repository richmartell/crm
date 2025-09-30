<?php

namespace App\Livewire;

use App\Models\ContactList;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layout')]
class ListForm extends Component
{
    public ?ContactList $list = null;

    public string $name = '';
    public string $description = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function mount(?ContactList $list = null): void
    {
        if ($list) {
            $this->ensureAuthorized($list);
            $this->list = $list;
            $this->name = $list->name;
            $this->description = (string) $list->description;
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->list) {
            $this->list->update([
                'name' => $this->name,
                'description' => $this->description ?: null,
            ]);

            session()->flash('status', 'List updated successfully.');
        } else {
            $this->list = ContactList::create([
                'user_id' => Auth::id(),
                'name' => $this->name,
                'description' => $this->description ?: null,
            ]);

            session()->flash('status', 'List created successfully.');
        }

        $this->dispatch('listUpdated');

        return redirect()->route('lists.show', $this->list);
    }

    public function render()
    {
        return view('livewire.list-form');
    }

    protected function ensureAuthorized(ContactList $list): void
    {
        abort_unless($list->user_id === Auth::id(), 403);
    }
}
