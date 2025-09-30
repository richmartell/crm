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

    <flux:card>
        <!-- Header with photo and basic info -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 p-8 -m-6 mb-6 rounded-t-lg">
            <div class="flex items-start gap-6">
                @if($contact->photo)
                    <flux:avatar src="{{ Storage::url($contact->photo) }}" size="2xl" class="border-4 border-white shadow-lg" />
                @else
                    <flux:avatar size="2xl" class="border-4 border-white shadow-lg">
                        {{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}
                    </flux:avatar>
                @endif

                <div class="flex-1 text-white">
                    <flux:heading size="2xl" class="mb-2 text-white">{{ $contact->full_name }}</flux:heading>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($contact->tags as $tag)
                            <flux:badge variant="pill" class="bg-white/20 backdrop-blur-sm border-0 text-white">
                                {{ $tag->name }}
                            </flux:badge>
                        @endforeach
                    </div>

                    <div class="space-y-2 text-white/90">
                        @if($contact->email)
                            <div class="flex items-center gap-2">
                                <flux:icon.envelope class="size-5" />
                                <a href="mailto:{{ $contact->email }}" class="hover:underline">{{ $contact->email }}</a>
                            </div>
                        @endif
                        
                        @if($contact->phone_number)
                            <div class="flex items-center gap-2">
                                <flux:icon.phone class="size-5" />
                                <a href="tel:{{ $contact->phone_number }}" class="hover:underline">{{ $contact->phone_number }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Details sections -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Personal Information -->
            <div>
                <flux:heading size="lg" class="mb-4">Personal Information</flux:heading>
                <flux:separator variant="subtle" class="mb-4" />
                <dl class="space-y-4">
                    @if($contact->date_of_birth)
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Date of Birth</dt>
                            <dd class="mt-1">
                                <flux:text>{{ $contact->date_of_birth->format('F d, Y') }}</flux:text>
                                <flux:text class="text-zinc-500">({{ $contact->date_of_birth->age }} years old)</flux:text>
                            </dd>
                        </div>
                    @endif

                    @if($contact->anniversary_date)
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Anniversary</dt>
                            <dd class="mt-1">
                                <flux:text>{{ $contact->anniversary_date->format('F d, Y') }}</flux:text>
                            </dd>
                        </div>
                    @endif

                    @if($contact->address)
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Address</dt>
                            <dd class="mt-1">
                                <flux:text>{{ $contact->address->formatted_address }}</flux:text>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Notes -->
            @if($contact->notes)
                <div>
                    <flux:heading size="lg" class="mb-4">Notes</flux:heading>
                    <flux:separator variant="subtle" class="mb-4" />
                    <flux:text class="whitespace-pre-wrap">{{ $contact->notes }}</flux:text>
                </div>
            @endif
        </div>

    </flux:card>

    <!-- Relationships Section -->
    <flux:card>
        <div class="flex items-center justify-between mb-6">
            <flux:heading size="lg">Relationships</flux:heading>
            @if($contact->user_id === auth()->id())
                <flux:button wire:click="openRelationshipModal" icon="plus" size="sm">
                    Add Relationship
                </flux:button>
            @endif
        </div>
        
        <flux:separator variant="subtle" class="mb-6" />

        @php
            $allRelationships = $contact->relationships->concat($contact->inverseRelationships);
        @endphp

        @if($allRelationships->isEmpty())
            <flux:text class="text-center py-8">No relationships added yet.</flux:text>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($contact->relationships as $relationship)
                    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    @if($relationship->relatedContact->photo)
                                        <flux:avatar src="{{ Storage::url($relationship->relatedContact->photo) }}" size="sm" />
                                    @else
                                        <flux:avatar size="sm">
                                            {{ substr($relationship->relatedContact->first_name, 0, 1) }}{{ substr($relationship->relatedContact->last_name, 0, 1) }}
                                        </flux:avatar>
                                    @endif
                                    <div>
                                        <a href="{{ route('contacts.show', $relationship->relatedContact->id) }}" class="font-medium hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $relationship->relatedContact->full_name }}
                                        </a>
                                        <flux:text size="xs" class="capitalize">{{ $relationship->relationship_type }}</flux:text>
                                    </div>
                                </div>
                            </div>
                            <flux:button 
                                wire:click="deleteRelationship({{ $relationship->id }})"
                                wire:confirm="Are you sure you want to remove this relationship?"
                                size="xs" 
                                variant="ghost"
                                icon="x-mark"
                                class="text-red-600"
                            />
                        </div>
                    </div>
                @endforeach

                @foreach($contact->inverseRelationships as $relationship)
                    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    @if($relationship->contact->photo)
                                        <flux:avatar src="{{ Storage::url($relationship->contact->photo) }}" size="sm" />
                                    @else
                                        <flux:avatar size="sm">
                                            {{ substr($relationship->contact->first_name, 0, 1) }}{{ substr($relationship->contact->last_name, 0, 1) }}
                                        </flux:avatar>
                                    @endif
                                    <div>
                                        <a href="{{ route('contacts.show', $relationship->contact->id) }}" class="font-medium hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $relationship->contact->full_name }}
                                        </a>
                                        <flux:text size="xs">Has {{ $relationship->relationship_type }}</flux:text>
                                    </div>
                                </div>
                            </div>
                            <flux:button 
                                wire:click="deleteRelationship({{ $relationship->id }})"
                                wire:confirm="Are you sure you want to remove this relationship?"
                                size="xs" 
                                variant="ghost"
                                icon="x-mark"
                                class="text-red-600"
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