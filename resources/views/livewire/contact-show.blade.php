<div class="p-6 max-w-5xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <flux:button href="{{ route('contacts.index') }}" icon="arrow-left" variant="ghost">
            Back to Contacts
        </flux:button>
        
        <div class="flex items-center gap-2">
            <flux:badge :color="$contact->is_shared ? 'blue' : 'gray'">
                {{ $contact->is_shared ? 'Shared contact' : 'Personal contact' }}
            </flux:badge>

            @if($contact->user_id === auth()->id())
                <flux:button href="{{ route('contacts.edit', $contact->id) }}" icon="pencil">
                    Edit
                </flux:button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header with photo and basic info -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-8">
            <div class="flex items-start gap-6">
                @if($contact->photo)
                    <img src="{{ Storage::url($contact->photo) }}" alt="{{ $contact->full_name }}" class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
                @else
                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-white flex items-center justify-center">
                        <span class="text-4xl font-bold text-gray-700">
                            {{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}
                        </span>
                    </div>
                @endif

                <div class="flex-1 text-white">
                    <h1 class="text-3xl font-bold mb-2">{{ $contact->full_name }}</h1>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($contact->tags as $tag)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>

                    <div class="space-y-2 text-white/90">
                        @if($contact->email)
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <a href="mailto:{{ $contact->email }}" class="hover:underline">{{ $contact->email }}</a>
                            </div>
                        @endif
                        
                        @if($contact->phone_number)
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <a href="tel:{{ $contact->phone_number }}" class="hover:underline">{{ $contact->phone_number }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Details sections -->
        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Personal Information -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h2>
                <dl class="space-y-3">
                    @if($contact->date_of_birth)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $contact->date_of_birth->format('F d, Y') }}
                                <span class="text-gray-500">({{ $contact->date_of_birth->age }} years old)</span>
                            </dd>
                        </div>
                    @endif

                    @if($contact->anniversary_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Anniversary</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $contact->anniversary_date->format('F d, Y') }}
                            </dd>
                        </div>
                    @endif

                    @if($contact->address)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $contact->address->formatted_address }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Notes -->
            @if($contact->notes)
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Notes</h2>
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $contact->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Relationships Section -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Relationships</h2>
                @if($contact->user_id === auth()->id())
                    <flux:button wire:click="openRelationshipModal" icon="plus" size="sm">
                        Add Relationship
                    </flux:button>
                @endif
            </div>

            @php
                $allRelationships = $contact->relationships->concat($contact->inverseRelationships);
            @endphp

            @if($allRelationships->isEmpty())
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">No relationships added yet.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($contact->relationships as $relationship)
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        @if($relationship->relatedContact->photo)
                                            <img src="{{ Storage::url($relationship->relatedContact->photo) }}" alt="{{ $relationship->relatedContact->full_name }}" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                                <span class="text-white text-sm font-semibold">
                                                    {{ substr($relationship->relatedContact->first_name, 0, 1) }}{{ substr($relationship->relatedContact->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('contacts.show', $relationship->relatedContact->id) }}" class="font-medium text-gray-900 dark:text-white hover:text-blue-600">
                                                {{ $relationship->relatedContact->full_name }}
                                            </a>
                                            <p class="text-xs text-gray-500 capitalize">{{ $relationship->relationship_type }}</p>
                                        </div>
                                    </div>
                                </div>
                                <flux:button 
                                    wire:click="deleteRelationship({{ $relationship->id }})"
                                    wire:confirm="Are you sure you want to remove this relationship?"
                                    size="xs" 
                                    variant="ghost"
                                    class="text-red-600"
                                >
                                    ×
                                </flux:button>
                            </div>
                        </div>
                    @endforeach

                    @foreach($contact->inverseRelationships as $relationship)
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        @if($relationship->contact->photo)
                                            <img src="{{ Storage::url($relationship->contact->photo) }}" alt="{{ $relationship->contact->full_name }}" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                                <span class="text-white text-sm font-semibold">
                                                    {{ substr($relationship->contact->first_name, 0, 1) }}{{ substr($relationship->contact->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('contacts.show', $relationship->contact->id) }}" class="font-medium text-gray-900 dark:text-white hover:text-blue-600">
                                                {{ $relationship->contact->full_name }}
                                            </a>
                                            <p class="text-xs text-gray-500">Has {{ $relationship->relationship_type }}</p>
                                        </div>
                                    </div>
                                </div>
                                <flux:button 
                                    wire:click="deleteRelationship({{ $relationship->id }})"
                                    wire:confirm="Are you sure you want to remove this relationship?"
                                    size="xs" 
                                    variant="ghost"
                                    class="text-red-600"
                                >
                                    ×
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Add Relationship Modal -->
    @if($showRelationshipModal)
        <flux:modal wire:model="showRelationshipModal" class="max-w-md">
            <form wire:submit="addRelationship">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold">Add Relationship</h3>
                    
                    <flux:select wire:model="relatedContactId" label="Contact" required>
                        <option value="">Select a contact...</option>
                        @foreach($availableContacts as $availableContact)
                            <option value="{{ $availableContact->id }}">{{ $availableContact->full_name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model="relationshipType" label="Relationship Type" placeholder="e.g., spouse, child, parent, friend" required />

                    <div class="flex gap-2 justify-end">
                        <flux:button type="button" wire:click="closeRelationshipModal" variant="ghost">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            Add Relationship
                        </flux:button>
                    </div>
                </div>
            </form>
        </flux:modal>
    @endif
</div>