<div class="py-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <flux:heading size="xl">{{ $list->name }}</flux:heading>
                @if($list->archived_at)
                    <flux:badge color="zinc" size="sm">Archived</flux:badge>
                @endif
            </div>
            <flux:subheading>{{ $list->description ?: 'No description provided.' }}</flux:subheading>
            <flux:text size="sm" class="mt-1">Created {{ $list->created_at->format('F j, Y') }}</flux:text>
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
        <flux:callout variant="success" icon="check-circle">
            {{ session('status') }}
        </flux:callout>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-4">
            <div class="flex items-center gap-2">
                <flux:icon.users class="size-5" />
                <flux:heading size="lg">List Members ({{ $list->contacts->count() }})</flux:heading>
            </div>

            @if($list->contacts->isEmpty())
                <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-700 p-8 text-center">
                    <p class="text-gray-600 dark:text-gray-400">No contacts in this list yet. Add some from the right panel.</p>
                </div>
            @else
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Contact Name</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach($list->contacts as $contact)
                            <flux:table.row :key="$contact->id">
                                <flux:table.cell>
                                    <a href="{{ route('contacts.show', $contact) }}" class="font-medium text-gray-900 dark:text-white hover:underline">
                                        {{ $contact->full_name }}
                                    </a>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:button icon="trash" variant="ghost" size="sm" wire:click="removeContact({{ $contact->id }})" wire:confirm="Remove {{ $contact->full_name }} from this list?" />
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </div>

        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <flux:icon.plus-circle class="size-5" />
                <flux:heading size="lg">Add Contacts</flux:heading>
            </div>
            <flux:input wire:model.live.debounce.300ms="contactSearch" placeholder="Search contacts..." icon="magnifying-glass" />

            @if($availableContacts->isEmpty())
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No contacts available to add.</p>
                </div>
            @else
                <flux:checkbox.group wire:model="selectedContacts">
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($availableContacts as $contact)
                            <label class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer">
                                <flux:checkbox value="{{ $contact->id }}" />
                                <span class="font-medium text-gray-900 dark:text-white">{{ $contact->full_name }}</span>
                            </label>
                        @endforeach
                    </div>
                </flux:checkbox.group>

                <flux:pagination :paginator="$availableContacts" />

                <flux:button variant="primary" icon="user-plus" class="w-full" wire:click="addContacts">
                    Add Selected Contacts ({{ count($selectedContacts) }})
                </flux:button>
            @endif
        </div>
    </div>
</div>
