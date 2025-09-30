<div class="py-6 space-y-6">
    <div class="flex items-center justify-between">
        <flux:button href="{{ route('contacts.index') }}" icon="arrow-left" variant="ghost">
            Back to Contacts
        </flux:button>
        
        <div class="flex items-center gap-2">
            <flux:badge :color="$contact->is_shared ? 'blue' : 'zinc'" size="sm">
                {{ $contact->is_shared ? 'Shared' : 'Personal' }}
            </flux:badge>

            @if($contact->user_id === auth()->id())
                <flux:button href="{{ route('contacts.edit', $contact->id) }}" icon="pencil" variant="primary">
                    Edit
                </flux:button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif

    <flux:card class="overflow-visible">
        <!-- Header with photo and basic info -->
        <div class="flex flex-col md:flex-row items-start gap-6 pb-6 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex-shrink-0">
                @if($contact->photo)
                    <flux:avatar src="{{ Storage::url($contact->photo) }}" size="2xl" class="ring-4 ring-white dark:ring-zinc-900 shadow-xl" />
                @else
                    <flux:avatar size="2xl" class="ring-4 ring-white dark:ring-zinc-900 shadow-xl bg-gradient-to-br from-blue-500 to-purple-600">
                        <span class="text-3xl font-bold text-white">
                            {{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}
                        </span>
                    </flux:avatar>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <flux:heading size="2xl">{{ $contact->full_name }}</flux:heading>
                </div>
                
                @if($contact->tags->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($contact->tags as $tag)
                            <flux:badge 
                                size="sm"
                                variant="pill"
                                style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border-color: {{ $tag->color }};"
                            >
                                {{ $tag->name }}
                            </flux:badge>
                        @endforeach
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @if($contact->email)
                        <div class="flex items-center gap-2 text-sm">
                            <flux:icon.envelope class="size-4 text-zinc-400" />
                            <a href="mailto:{{ $contact->email }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 hover:underline truncate">
                                {{ $contact->email }}
                            </a>
                        </div>
                    @endif
                    
                    @if($contact->phone_number)
                        <div class="flex items-center gap-2 text-sm">
                            <flux:icon.phone class="size-4 text-zinc-400" />
                            <a href="tel:{{ $contact->phone_number }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 hover:underline">
                                {{ $contact->phone_number }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Details sections -->
        @if($contact->date_of_birth || $contact->anniversary_date || $contact->address || $contact->notes)
            <flux:separator variant="subtle" class="my-6" />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                @if($contact->date_of_birth || $contact->anniversary_date || $contact->address)
                    <div class="space-y-4">
                        <flux:heading size="lg" class="flex items-center gap-2">
                            <flux:icon.calendar class="size-5" />
                            Personal Information
                        </flux:heading>
                        
                        <div class="space-y-3">
                            @if($contact->date_of_birth)
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-900">
                                    <flux:icon.cake class="size-5 text-zinc-400 mt-0.5" />
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">Birthday</div>
                                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $contact->date_of_birth->format('F d, Y') }} ({{ $contact->date_of_birth->age }} years old)
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($contact->anniversary_date)
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-900">
                                    <flux:icon.heart class="size-5 text-zinc-400 mt-0.5" />
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">Anniversary</div>
                                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $contact->anniversary_date->format('F d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($contact->address)
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-900">
                                    <flux:icon.map-pin class="size-5 text-zinc-400 mt-0.5" />
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">Address</div>
                                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                            {{ $contact->address->formatted_address }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Notes -->
                @if($contact->notes)
                    <div class="space-y-4">
                        <flux:heading size="lg" class="flex items-center gap-2">
                            <flux:icon.document-text class="size-5" />
                            Notes
                        </flux:heading>
                        
                        <div class="p-4 rounded-lg bg-zinc-50 dark:bg-zinc-900">
                            <flux:text class="text-sm whitespace-pre-wrap">{{ $contact->notes }}</flux:text>
                        </div>
                    </div>
                @endif
            </div>
        @endif

    </flux:card>

    <!-- Relationships Section -->
    <flux:card>
        <div class="flex items-center justify-between mb-6">
            <flux:heading size="lg" class="flex items-center gap-2">
                <flux:icon.user-group class="size-5" />
                Relationships
            </flux:heading>
            @if($contact->user_id === auth()->id())
                <flux:button wire:click="openRelationshipModal" icon="plus" size="sm" variant="primary">
                    Add Relationship
                </flux:button>
            @endif
        </div>
        
        <flux:separator variant="subtle" class="mb-6" />

        @php
            $allRelationships = $contact->relationships->concat($contact->inverseRelationships);
        @endphp

        @if($allRelationships->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 mb-4">
                    <flux:icon.user-group class="size-6 text-zinc-400" />
                </div>
                <flux:text class="text-zinc-500">No relationships added yet.</flux:text>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($contact->relationships as $relationship)
                    <div class="group relative bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700 hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                @if($relationship->relatedContact->photo)
                                    <flux:avatar src="{{ Storage::url($relationship->relatedContact->photo) }}" size="sm" />
                                @else
                                    <flux:avatar size="sm" class="bg-gradient-to-br from-blue-500 to-purple-600">
                                        <span class="text-white font-semibold">
                                            {{ substr($relationship->relatedContact->first_name, 0, 1) }}{{ substr($relationship->relatedContact->last_name, 0, 1) }}
                                        </span>
                                    </flux:avatar>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('contacts.show', $relationship->relatedContact->id) }}" class="block font-medium text-zinc-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors truncate">
                                    {{ $relationship->relatedContact->full_name }}
                                </a>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 capitalize mt-0.5">
                                    {{ $relationship->relationship_type }}
                                </div>
                            </div>
                            <flux:button 
                                wire:click="deleteRelationship({{ $relationship->id }})"
                                wire:confirm="Are you sure you want to remove this relationship?"
                                size="xs" 
                                variant="ghost"
                                icon="x-mark"
                                class="opacity-0 group-hover:opacity-100 transition-opacity text-red-600 hover:text-red-700"
                            />
                        </div>
                    </div>
                @endforeach

                @foreach($contact->inverseRelationships as $relationship)
                    <div class="group relative bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700 hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                @if($relationship->contact->photo)
                                    <flux:avatar src="{{ Storage::url($relationship->contact->photo) }}" size="sm" />
                                @else
                                    <flux:avatar size="sm" class="bg-gradient-to-br from-blue-500 to-purple-600">
                                        <span class="text-white font-semibold">
                                            {{ substr($relationship->contact->first_name, 0, 1) }}{{ substr($relationship->contact->last_name, 0, 1) }}
                                        </span>
                                    </flux:avatar>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('contacts.show', $relationship->contact->id) }}" class="block font-medium text-zinc-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors truncate">
                                    {{ $relationship->contact->full_name }}
                                </a>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    Has {{ $relationship->relationship_type }}
                                </div>
                            </div>
                            <flux:button 
                                wire:click="deleteRelationship({{ $relationship->id }})"
                                wire:confirm="Are you sure you want to remove this relationship?"
                                size="xs" 
                                variant="ghost"
                                icon="x-mark"
                                class="opacity-0 group-hover:opacity-100 transition-opacity text-red-600 hover:text-red-700"
                            />
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </flux:card>

    <!-- Add Relationship Modal -->
    @if($showRelationshipModal)
        <flux:modal wire:model="showRelationshipModal" class="space-y-6">
            <form wire:submit="addRelationship" class="space-y-6">
                <flux:heading size="lg">Add Relationship</flux:heading>
                <flux:separator variant="subtle" />
                
                <flux:field>
                    <flux:label>Contact</flux:label>
                    <flux:select wire:model="relatedContactId" required>
                        <option value="">Select a contact...</option>
                        @foreach($availableContacts as $availableContact)
                            <option value="{{ $availableContact->id }}">{{ $availableContact->full_name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="relatedContactId" />
                </flux:field>

                <flux:field>
                    <flux:label>Relationship Type</flux:label>
                    <flux:input wire:model="relationshipType" placeholder="e.g., spouse, child, parent, friend" required />
                    <flux:description>Describe how this person relates to the contact</flux:description>
                    <flux:error name="relationshipType" />
                </flux:field>

                <flux:separator variant="subtle" />

                <div class="flex gap-2 justify-end">
                    <flux:button type="button" wire:click="closeRelationshipModal" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        Add Relationship
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>