<div class="py-6 space-y-6">
    <div class="flex items-center justify-between">
        <flux:button href="{{ route('contacts.index') }}" icon="arrow-left" variant="ghost">
            Back to Contacts
        </flux:button>
        
        <div class="flex items-center gap-2">
            <flux:badge :color="$contact->is_shared ? 'blue' : 'zinc'" size="sm">
                {{ $contact->is_shared ? 'Shared' : 'Personal' }}
            </flux:badge>

            @if($contact->user_id === auth()->id() || $contact->is_shared)
                <flux:button href="{{ route('contacts.edit', $contact->id) }}" icon="pencil" size="xs" variant="primary" iconOnly>
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
                @if($contact->avatar_url)
                    <flux:avatar src="{{ $contact->avatar_url }}" size="2xl" class="ring-4 ring-white dark:ring-zinc-900 shadow-xl" />
                @else
                    <flux:avatar size="2xl" class="ring-4 ring-white dark:ring-zinc-900 shadow-xl bg-gradient-to-br from-blue-500 to-purple-600">
                        <span class="text-3xl font-bold text-white">
                            {{ $contact->initials }}
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
            @if($contact->user_id === auth()->id() || $contact->is_shared)
                <flux:button wire:click="openRelationshipModal" icon="plus" size="sm" variant="primary">
                    Add Relationship
                </flux:button>
            @endif
        </div>
        
        <flux:separator variant="subtle" class="mb-6" />

        @if($contact->relationships->isEmpty())
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
                                @if($relationship->relatedContact->avatar_url)
                                    <flux:avatar src="{{ $relationship->relatedContact->avatar_url }}" size="sm" />
                                @else
                                    <flux:avatar size="sm" class="bg-gradient-to-br from-blue-500 to-purple-600">
                                        <span class="text-white font-semibold">
                                            {{ $relationship->relatedContact->initials }}
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
            </div>
        @endif
    </flux:card>

    <!-- Lists Section -->
    <flux:card>
        <div class="flex items-center justify-between mb-6">
            <flux:heading size="lg" class="flex items-center gap-2">
                <flux:icon.clipboard-document-list class="size-5" />
                Lists
            </flux:heading>
        </div>
        
        <flux:separator variant="subtle" class="mb-6" />

        @if($contact->lists->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 mb-4">
                    <flux:icon.clipboard-document-list class="size-6 text-zinc-400" />
                </div>
                <flux:text class="text-zinc-500">Not added to any lists yet.</flux:text>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($contact->lists as $list)
                    <a href="{{ route('lists.show', $list->id) }}" class="block group">
                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700 hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="size-10 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40">
                                        <flux:icon.clipboard-document-list class="size-5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-zinc-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">
                                        {{ $list->name }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                        @if($list->pivot->added_at)
                                            Added {{ \Carbon\Carbon::parse($list->pivot->added_at)->format('M j, Y') }}
                                        @else
                                            Added recently
                                        @endif
                                    </div>
                                </div>
                                <flux:icon.arrow-right class="size-4 text-zinc-400 group-hover:text-blue-500 transition-colors flex-shrink-0 mt-1" />
                            </div>
                        </div>
                    </a>
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
                
                <!-- Toggle between existing and new contact -->
                <div class="flex items-center gap-3 p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg">
                    <flux:checkbox wire:model.live="isCreatingNewContact" id="createNew" />
                    <label for="createNew" class="text-sm font-medium cursor-pointer">
                        Create a new contact
                    </label>
                </div>

                @if($isCreatingNewContact)
                    <!-- New Contact Form -->
                    <div class="space-y-4">
                        <flux:heading size="md">New Contact Details</flux:heading>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>First Name</flux:label>
                                <flux:input wire:model="newContactFirstName" required />
                                <flux:error name="newContactFirstName" />
                            </flux:field>
                            
                            <flux:field>
                                <flux:label>Last Name</flux:label>
                                <flux:input wire:model="newContactLastName" required />
                                <flux:error name="newContactLastName" />
                            </flux:field>
                        </div>
                        
                        <flux:field>
                            <flux:label>Date of Birth (Optional)</flux:label>
                            <flux:input wire:model="newContactDateOfBirth" type="date" />
                            <flux:error name="newContactDateOfBirth" />
                        </flux:field>
                        
                        <flux:field>
                            <flux:label>Email (Optional)</flux:label>
                            <flux:input wire:model="newContactEmail" type="email" placeholder="email@example.com" />
                            <flux:error name="newContactEmail" />
                        </flux:field>

                        @if($contact->address)
                            <flux:field variant="inline">
                                <flux:checkbox wire:model="sameAddress" id="sameAddress" />
                                <flux:label for="sameAddress">Lives at the same address as {{ $contact->full_name }}</flux:label>
                            </flux:field>
                        @endif
                    </div>
                @else
                    <!-- Select Existing Contact -->
                    <flux:field>
                        <flux:label>Select an existing contact</flux:label>
                        <flux:autocomplete wire:model="relatedContactName" placeholder="Start typing a name..." required>
                            @foreach($availableContacts as $availableContact)
                                <flux:autocomplete.item>{{ $availableContact->full_name }}</flux:autocomplete.item>
                            @endforeach
                        </flux:autocomplete>
                        <flux:description>Type to search and select from your contacts</flux:description>
                        <flux:error name="relatedContactName" />
                    </flux:field>
                @endif

                <!-- Relationship Type (common for both) -->
                <flux:field>
                    <flux:label>
                        @if($isCreatingNewContact)
                            This new person is {{ $contact->full_name }}'s...
                        @else
                            This person is {{ $contact->full_name }}'s...
                        @endif
                    </flux:label>
                    <flux:select wire:model="relationshipType" required>
                        <option value="">Select relationship...</option>
                        <option value="Parent">Parent</option>
                        <option value="Child">Child</option>
                        <option value="Spouse">Spouse</option>
                    </flux:select>
                    <flux:description>The reciprocal relationship will be added automatically.</flux:description>
                    <flux:error name="relationshipType" />
                </flux:field>

                <flux:separator variant="subtle" />

                <div class="flex gap-2 justify-end">
                    <flux:button type="button" wire:click="closeRelationshipModal" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        @if($isCreatingNewContact)
                            Create & Add Relationship
                        @else
                            Add Relationship
                        @endif
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>