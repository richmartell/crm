<div class="py-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <flux:heading size="xl">Contacts</flux:heading>
            <flux:subheading>Manage your personal and shared relationships</flux:subheading>
        </div>
        <flux:button href="{{ route('contacts.create') }}" icon="plus" variant="primary">
            Add Contact
        </flux:button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <flux:input wire:model.live.debounce.400ms="search" placeholder="Search contacts..." icon="magnifying-glass" />
        <flux:select wire:model.live="tagFilter" placeholder="Filter by tag">
            <option value="">All tags</option>
            @foreach($tags as $tag)
                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
            @endforeach
        </flux:select>
    </div>

    @if($contacts->isEmpty())
        <flux:card class="text-center">
            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 mb-4">
                <flux:icon.users class="size-6 text-blue-600 dark:text-blue-300" />
            </div>
            <flux:heading size="lg">No contacts yet</flux:heading>
            <flux:subheading class="mb-6">Start by adding someone you know.</flux:subheading>
            <flux:button href="{{ route('contacts.create') }}" variant="primary" icon="plus">
                Create contact
            </flux:button>
        </flux:card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($contacts as $contact)
                <flux:card class="flex flex-col">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1 min-w-0">
                            <flux:heading size="lg" class="truncate">
                                <a href="{{ route('contacts.show', $contact) }}" class="hover:underline">
                                    {{ $contact->first_name }} {{ $contact->last_name }}
                                </a>
                            </flux:heading>
                            <flux:subheading class="truncate">{{ $contact->email ?? 'No email' }}</flux:subheading>
                        </div>
                        <flux:badge :color="$contact->is_shared ? 'blue' : 'zinc'" size="sm">
                            {{ $contact->is_shared ? 'Shared' : 'Personal' }}
                        </flux:badge>
                    </div>

                    @if($contact->phone_number)
                        <div class="flex items-center gap-2 text-sm mb-2">
                            <flux:icon.phone class="size-4" />
                            <flux:text>{{ $contact->phone_number }}</flux:text>
                        </div>
                    @endif

                    @if($contact->address)
                        <div class="flex items-start gap-2 text-sm mb-3">
                            <flux:icon.map-pin class="size-4 mt-0.5" />
                            <flux:text>{{ $contact->address->formatted_address }}</flux:text>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2 mt-auto">
                        @foreach($contact->tags as $tag)
                            <flux:badge 
                                size="sm" 
                                variant="pill"
                                style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};"
                            >
                                {{ $tag->name }}
                            </flux:badge>
                        @endforeach
                    </div>
                </flux:card>
            @endforeach
        </div>

        <flux:pagination :paginator="$contacts" />
    @endif
</div>