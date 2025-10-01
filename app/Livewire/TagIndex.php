<?php

namespace App\Livewire;

use App\Models\Tag;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layout')]
class TagIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $color = '#3b82f6';

    protected $listeners = ['tagUpdated' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['name', 'color', 'editingId']);
        $this->color = '#3b82f6';
        $this->showModal = true;
    }

    public function openEditModal(int $tagId): void
    {
        $tag = Tag::findOrFail($tagId);
        $this->editingId = $tag->id;
        $this->name = $tag->name;
        $this->color = $tag->color;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'color', 'editingId']);
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:20',
            'color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
        ], [
            'name.max' => 'The tag name cannot be longer than 20 characters.',
            'color.regex' => 'Please select a valid color.',
        ]);

        if ($this->editingId) {
            $tag = Tag::findOrFail($this->editingId);
            $tag->update([
                'name' => $this->name,
                'color' => $this->color,
            ]);
            session()->flash('success', 'Tag updated successfully.');
        } else {
            Tag::create([
                'name' => $this->name,
                'color' => $this->color,
            ]);
            session()->flash('success', 'Tag created successfully.');
        }

        $this->closeModal();
    }

    public function delete(int $tagId): void
    {
        $tag = Tag::findOrFail($tagId);
        
        // Check if tag is in use
        if ($tag->contacts()->count() > 0) {
            session()->flash('error', 'Cannot delete tag that is assigned to contacts.');
            return;
        }

        $tag->delete();
        session()->flash('success', 'Tag deleted successfully.');
    }

    public function getTagsProperty()
    {
        return Tag::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->withCount('contacts')
            ->orderBy('name')
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.tag-index', [
            'tags' => $this->tags,
        ]);
    }
}

