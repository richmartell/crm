<div class="py-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <flux:heading size="xl">Lists</flux:heading>
            <flux:subheading>Create and manage groups of contacts for events, holidays, and more.</flux:subheading>
        </div>
        <flux:button href="{{ route('lists.create') }}" icon="plus" variant="primary">
            New List
        </flux:button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Search lists..." icon="magnifying-glass" class="md:col-span-2" />
        <flux:button variant="subtle" icon="archive-box" wire:click="toggleArchived">
            {{ $showArchived ? 'Showing Archived' : 'Showing Active' }}
        </flux:button>
    </div>

    @if($lists->isEmpty())
        <flux:card class="text-center">
            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 mb-4">
                <flux:icon.clipboard-document-list class="size-6 text-blue-600 dark:text-blue-300" />
            </div>
            <flux:heading size="lg">No lists found</flux:heading>
            <flux:subheading class="mb-6">Start by creating a new list for your next event.</flux:subheading>
            <flux:button href="{{ route('lists.create') }}" variant="primary" icon="plus">
                Create list
            </flux:button>
        </flux:card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($lists as $list)
                <a href="{{ route('lists.show', $list) }}" class="block">
                    <flux:card class="hover:border-blue-500 dark:hover:border-blue-400 transition-colors cursor-pointer h-full">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <flux:heading size="lg">{{ $list->name }}</flux:heading>
                                <flux:subheading>Created {{ $list->created_at->format('M j, Y') }}</flux:subheading>
                            </div>
                            @if($list->archived_at)
                                <flux:badge color="zinc" size="sm">Archived</flux:badge>
                            @endif
                        </div>
                        
                        @if($list->description)
                            <flux:text class="mb-4">{{ Str::limit($list->description, 120) }}</flux:text>
                        @endif
                        
                        <div class="flex items-center justify-between">
                            <flux:badge color="blue" size="sm" icon="users">
                                {{ $list->contacts()->count() }} contacts
                            </flux:badge>
                            <div class="flex items-center gap-1 text-sm text-zinc-600 dark:text-zinc-400">
                                <flux:icon.arrow-right class="size-4" />
                                <span>View list</span>
                            </div>
                        </div>
                    </flux:card>
                </a>
            @endforeach
        </div>

        <flux:pagination :paginator="$lists" />
    @endif
</div>
