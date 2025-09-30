<div class="p-6 max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $list->name }}</h1>
                @if($list->archived_at)
                    <flux:badge variant="subtle" color="gray">Archived</flux:badge>
                @endif
            </div>
            <p class="text-gray-600 dark:text-gray-400">{{ $list->description ?: 'No description provided.' }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Created {{ $list->created_at->format('F j, Y') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <flux:button href="{{ route('lists.edit', $list) }}" variant="ghost" icon="pencil">
                Edit
            </flux:button>
            <flux:button icon="arrow-up-tray" variant="subtle" wire:click="export">
                Export CSV
            </flux:button>
            @if($list->archived_at)
                <flux:button icon="arrow-path" variant="primary" wire:click="restore">
                    Restore
                </flux:button>
            @else
                <flux:button icon="archive-box" variant="ghost" wire:click="archive">
                    Archive
                </flux:button>
            @endif
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-lg border border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/30 p-4 text-sm text-blue-700 dark:text-blue-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <flux:icon name="users" class="size-5" />
                List Members ({{ $list->contacts->count() }})
            </h2>

            @if($list->contacts->isEmpty())
                <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-700 p-8 text-center">
                    <p class="text-gray-600 dark:text-gray-400">No contacts in this list yet. Add some from the right panel.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($list->contacts as $contact)
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                            <div>
                                <a href="{{ route('contacts.show', $contact) }}" class="text-lg font-semibold text-gray-900 dark:text-white hover:underline">
                                    {{ $contact->full_name }}
                                </a>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $contact->email ?? 'No email' }} • {{ $contact->phone_number ?? 'No phone' }}
                                </div>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($contact->tags as $tag)
                                        <span class="text-xs font-semibold px-3 py-1 rounded-full" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <flux:button icon="trash" variant="ghost" wire:click="removeContact({{ $contact->id }})" wire:confirm="Remove {{ $contact->full_name }} from this list?" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <flux:icon name="plus-circle" class="size-5" />
                Add Contacts
            </h2>
            <flux:input wire:model.debounce.300ms="contactSearch" placeholder="Search contacts..." icon="magnifying-glass" />

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($availableContacts as $contact)
                    <label class="flex items-start gap-3 p-4">
                        <flux:checkbox wire:model="selectedContacts" value="{{ $contact->id }}" />
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $contact->full_name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->email ?? 'No email' }}</p>
                        </div>
                    </label>
                @empty
                    <div class="p-4 text-sm text-gray-500 dark:text-gray-400">No contacts available to add.</div>
                @endforelse
            </div>

            {{ $availableContacts->links() }}

            <flux:button variant="primary" icon="user-plus" class="w-full" wire:click="addContacts" :disabled="empty($selectedContacts)">
                Add Selected Contacts
            </flux:button>
        </div>
    </div>
</div>
