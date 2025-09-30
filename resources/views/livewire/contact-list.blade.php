<div class="p-6 max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Contacts</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage your personal and family contacts</p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        <div class="flex-1 flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search contacts..." 
                class="flex-1 min-w-64"
                clearable
            />
            
            <flux:select wire:model.live="selectedTag" placeholder="Filter by tag" class="w-full sm:w-48">
                <option value="">All tags</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
            </flux:select>
            
            @if($search || $selectedTag)
                <flux:button wire:click="clearFilters" variant="ghost">
                    Clear filters
                </flux:button>
            @endif
        </div>

        <flux:button href="{{ route('contacts.create') }}" icon="plus" variant="primary">
            Add Contact
        </flux:button>
    </div>

    @if($contacts->isEmpty())
        <div class="text-center py-16">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No contacts found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new contact.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($contacts as $contact)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                @if($contact->photo)
                                    <img src="{{ Storage::url($contact->photo) }}" alt="{{ $contact->full_name }}" class="w-16 h-16 rounded-full object-cover mb-3">
                                @else
                                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center mb-3">
                                        <span class="text-white text-xl font-semibold">
                                            {{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                                
                                <a href="{{ route('contacts.show', $contact->id) }}" class="block">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $contact->full_name }}
                                    </h3>
                                </a>
                                
                                @if($contact->email)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 truncate">{{ $contact->email }}</p>
                                @endif
                                
                                @if($contact->phone_number)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ $contact->phone_number }}</p>
                                @endif
                            </div>
                        </div>

                        @if($contact->tags->isNotEmpty())
                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach($contact->tags as $tag)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if($contact->address)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 truncate">
                                📍 {{ $contact->address->city }}{{ $contact->address->country ? ', ' . $contact->address->country : '' }}
                            </p>
                        @endif

                        <div class="flex gap-2">
                            <flux:button href="{{ route('contacts.edit', $contact->id) }}" size="sm" variant="ghost" class="flex-1">
                                Edit
                            </flux:button>
                            <flux:button 
                                wire:click="deleteContact({{ $contact->id }})" 
                                wire:confirm="Are you sure you want to delete this contact?"
                                size="sm" 
                                variant="ghost"
                                class="text-red-600 hover:text-red-700"
                            >
                                Delete
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $contacts->links() }}
        </div>
    @endif
</div>