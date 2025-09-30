<div class="p-6 max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Lists</h1>
            <p class="text-gray-600 dark:text-gray-400">Create and manage groups of contacts for events, holidays, and more.</p>
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
        <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-700 p-12 text-center">
            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 mb-4">
                <flux:icon name="clipboard-document-list" class="size-6 text-blue-600 dark:text-blue-300" />
            </div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No lists found</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Start by creating a new list for your next event.</p>
            <flux:button href="{{ route('lists.create') }}" variant="primary" icon="plus">
                Create list
            </flux:button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($lists as $list)
                <a href="{{ route('lists.show', $list) }}" class="block rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-blue-500 transition">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $list->name }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Created {{ $list->created_at->format('M j, Y') }}</p>
                        </div>
                        @if($list->archived_at)
                            <flux:badge variant="subtle" color="gray">Archived</flux:badge>
                        @endif
                    </div>
                    @if($list->description)
                        <p class="text-gray-700 dark:text-gray-300 mb-4">{{ Str::limit($list->description, 120) }}</p>
                    @endif
                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                        <span>{{ $list->contacts()->count() }} contacts</span>
                        <span class="flex items-center gap-1">
                            <flux:icon name="arrow-right" class="size-4" />
                            View list
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div>
            {{ $lists->links() }}
        </div>
    @endif
</div>
