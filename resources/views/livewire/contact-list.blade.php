<div class="p-6 max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Contacts</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage your personal and shared relationships</p>
        </div>
        <flux:button href="{{ route('contacts.create') }}" icon="plus" variant="primary">
            Add Contact
        </flux:button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
        <div class="lg:col-span-2">
            <flux:input wire:model.live.debounce.400ms="search" placeholder="Search contacts..." icon="magnifying-glass" />
        </div>
        <div>
            <flux:select wire:model.live="tagFilter" placeholder="Filter by tag">
                <option value="">All tags</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div>
            <flux:select wire:model.live="sortField" label="Sort by">
                <option value="first_name">First name</option>
                <option value="last_name">Last name</option>
                <option value="created_at">Created date</option>
            </flux:select>
        </div>
    </div>

    @if($contacts->isEmpty())
        <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-700 p-12 text-center">
            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 mb-4">
                <flux:icon name="users" class="size-6 text-blue-600 dark:text-blue-300" />
            </div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No contacts yet</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Start by adding someone you know.</p>
            <flux:button href="{{ route('contacts.create') }}" variant="primary" icon="plus">
                Create contact
            </flux:button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($contacts as $contact)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-5 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                <a href="{{ route('contacts.show', $contact) }}" class="hover:underline">
                                    {{ $contact->first_name }} {{ $contact->last_name }}
                                </a>
                            </h2>
                            <p class="text-sm text-gray-500">{{ $contact->email ?? 'No email' }}</p>
                        </div>
                        <flux:badge :color="$contact->is_shared ? 'blue' : 'gray'">
                            {{ $contact->is_shared ? 'Shared' : 'Personal' }}
                        </flux:badge>
                    </div>

                    @if($contact->phone_number)
                        <div class="flex items-center text-gray-600 dark:text-gray-400 text-sm mb-2">
                            <flux:icon name="phone" class="w-4 h-4 mr-2" />
                            {{ $contact->phone_number }}
                        </div>
                    @endif

                    @if($contact->address)
                        <div class="flex items-start text-gray-600 dark:text-gray-400 text-sm mb-3">
                            <flux:icon name="map-pin" class="w-4 h-4 mr-2 mt-0.5" />
                            <span>{{ $contact->address->formatted_address }}</span>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2 mt-auto">
                        @foreach($contact->tags as $tag)
                            <span class="text-xs font-semibold px-3 py-1 rounded-full" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $contacts->links() }}
        </div>
    @endif
</div>