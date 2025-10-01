<div class="py-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <flux:heading size="xl">Tags</flux:heading>
            <flux:subheading>Manage tags to organize and categorize your contacts.</flux:subheading>
        </div>
        <flux:button wire:click="openCreateModal" icon="plus" variant="primary">
            New Tag
        </flux:button>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" icon="exclamation-circle">
            {{ session('error') }}
        </flux:callout>
    @endif

    <!-- Search -->
    <flux:input 
        wire:model.live.debounce.300ms="search" 
        placeholder="Search tags..." 
        icon="magnifying-glass" 
    />

    @if($tags->isEmpty())
        <flux:card class="text-center">
            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 mb-4">
                <flux:icon.tag class="size-6 text-blue-600 dark:text-blue-300" />
            </div>
            <flux:heading size="lg">No tags found</flux:heading>
            <flux:subheading class="mb-6">Start by creating a new tag to organize your contacts.</flux:subheading>
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                Create tag
            </flux:button>
        </flux:card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($tags as $tag)
                <flux:card>
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div 
                                class="w-8 h-8 rounded-full flex-shrink-0" 
                                style="background-color: {{ $tag->color }};"
                            ></div>
                            <div class="min-w-0 flex-1">
                                <flux:heading size="lg" class="truncate">{{ $tag->name }}</flux:heading>
                                <flux:subheading>{{ $tag->contacts_count }} {{ Str::plural('contact', $tag->contacts_count) }}</flux:subheading>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 mt-4">
                        <flux:button 
                            wire:click="openEditModal({{ $tag->id }})" 
                            variant="ghost" 
                            size="sm"
                            icon="pencil"
                            class="flex-1"
                        >
                            Edit
                        </flux:button>
                        <flux:button 
                            wire:click="delete({{ $tag->id }})" 
                            wire:confirm="Are you sure you want to delete this tag?"
                            variant="ghost" 
                            size="sm"
                            icon="trash"
                            class="flex-1 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                        >
                            Delete
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>

        <flux:pagination :paginator="$tags" />
    @endif

    <!-- Modal for Create/Edit -->
    <flux:modal wire:model="showModal" name="tag-form" variant="flyout">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Tag' : 'Create Tag' }}</flux:heading>
                <flux:subheading>{{ $editingId ? 'Update tag details' : 'Add a new tag to organize your contacts' }}</flux:subheading>
            </div>

            <flux:separator variant="subtle" />

            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input 
                    wire:model="name" 
                    placeholder="e.g., Family, Work, Friends"
                    maxlength="20"
                />
                <flux:description>Maximum 20 characters</flux:description>
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Color</flux:label>
                <div class="space-y-3">
                    <input 
                        type="color" 
                        wire:model.live="color" 
                        class="h-12 w-full rounded-lg border border-zinc-300 dark:border-zinc-700 cursor-pointer"
                    />
                    <div class="flex items-center gap-2">
                        <div 
                            class="w-8 h-8 rounded-full border border-zinc-300 dark:border-zinc-700" 
                            style="background-color: {{ $color }};"
                        ></div>
                        <flux:input 
                            wire:model.live="color" 
                            placeholder="#3b82f6"
                            class="flex-1"
                        />
                    </div>
                </div>
                <flux:description>Choose a color to identify this tag</flux:description>
                <flux:error name="color" />
            </flux:field>

            <!-- Quick Color Presets -->
            <div>
                <flux:label>Quick Colors</flux:label>
                <div class="grid grid-cols-8 gap-2 mt-2">
                    @foreach([
                        '#ef4444', '#f59e0b', '#fbbf24', '#84cc16', 
                        '#10b981', '#14b8a6', '#3b82f6', '#6366f1',
                        '#8b5cf6', '#a855f7', '#ec4899', '#f43f5e',
                        '#64748b', '#78716c', '#0891b2', '#059669'
                    ] as $presetColor)
                        <button
                            type="button"
                            wire:click="$set('color', '{{ $presetColor }}')"
                            class="w-10 h-10 rounded-full border-2 transition-all hover:scale-110 {{ $color === $presetColor ? 'ring-2 ring-offset-2 ring-zinc-400 dark:ring-zinc-500' : 'border-zinc-300 dark:border-zinc-700' }}"
                            style="background-color: {{ $presetColor }};"
                        ></button>
                    @endforeach
                </div>
            </div>

            <flux:separator variant="subtle" />

            <div class="flex justify-end gap-3">
                <flux:button wire:click="closeModal" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editingId ? 'Update Tag' : 'Create Tag' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>

