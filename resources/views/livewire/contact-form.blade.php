<div class="py-6 space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ $contactId ? 'Edit Contact' : 'Create Contact' }}</flux:heading>
        <flux:button href="{{ route('contacts.index') }}" icon="arrow-left" variant="ghost">
            Back to Contacts
        </flux:button>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-8">
            <!-- Basic Information -->
            <div>
                <flux:heading size="lg" class="mb-4">Basic Information</flux:heading>
                <flux:separator variant="subtle" class="mb-4" />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>First Name</flux:label>
                        <flux:input wire:model="first_name" required />
                        <flux:error name="first_name" />
                    </flux:field>
                    
                    <flux:field>
                        <flux:label>Last Name</flux:label>
                        <flux:input wire:model="last_name" required />
                        <flux:error name="last_name" />
                    </flux:field>
                    
                    <flux:field>
                        <flux:label>Email</flux:label>
                        <flux:input wire:model="email" type="email" />
                        <flux:error name="email" />
                    </flux:field>
                    
                    <flux:field>
                        <flux:label>Phone Number</flux:label>
                        <flux:input wire:model="phone_number" />
                        <flux:error name="phone_number" />
                    </flux:field>
                    
                    <flux:field>
                        <flux:label>Date of Birth</flux:label>
                        <flux:input wire:model="date_of_birth" type="date" />
                        <flux:error name="date_of_birth" />
                    </flux:field>
                    
                    <flux:field>
                        <flux:label>Anniversary Date</flux:label>
                        <flux:input wire:model="anniversary_date" type="date" />
                        <flux:error name="anniversary_date" />
                    </flux:field>
                </div>
            </div>

            <!-- Photo Upload -->
            <div>
                <flux:heading size="lg" class="mb-4">Photo</flux:heading>
                <flux:separator variant="subtle" class="mb-4" />
                
                @if($existingPhoto && !$photo)
                    <div class="mb-4">
                        <flux:avatar src="{{ Storage::url($existingPhoto) }}" size="xl" />
                    </div>
                @endif

                @if($photo)
                    <div class="mb-4">
                        <flux:avatar src="{{ $photo->temporaryUrl() }}" size="xl" />
                    </div>
                @endif

                <flux:field>
                    <flux:label>Upload New Photo</flux:label>
                    <flux:input wire:model="photo" type="file" accept="image/*" />
                    <flux:description>Maximum file size: 2MB</flux:description>
                    <flux:error name="photo" />
                </flux:field>
            </div>

            <!-- Address -->
            <div>
                <flux:heading size="lg" class="mb-4">Address</flux:heading>
                <flux:separator variant="subtle" class="mb-4" />
                
                @if(!$createNewAddress && $addresses->isNotEmpty())
                    <div class="mb-4 space-y-3">
                        <flux:field>
                            <flux:label>Select Existing Address</flux:label>
                            <flux:select wire:model.live="address_id">
                                <option value="">None</option>
                                @foreach($addresses as $address)
                                    <option value="{{ $address->id }}">{{ $address->formatted_address }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                        
                        <flux:checkbox wire:model.live="createNewAddress" label="Create new address" />
                    </div>
                @endif

                @if($createNewAddress || $addresses->isEmpty() || $address_id)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field class="md:col-span-2">
                            <flux:label>Street</flux:label>
                            <flux:input wire:model="street" />
                            <flux:error name="street" />
                        </flux:field>
                        
                        <flux:field>
                            <flux:label>City</flux:label>
                            <flux:input wire:model="city" />
                            <flux:error name="city" />
                        </flux:field>
                        
                        <flux:field>
                            <flux:label>Postcode</flux:label>
                            <flux:input wire:model="postcode" />
                            <flux:error name="postcode" />
                        </flux:field>
                        
                        <flux:field class="md:col-span-2">
                            <flux:label>Country</flux:label>
                            <flux:input wire:model="country" />
                            <flux:error name="country" />
                        </flux:field>
                    </div>
                @endif
            </div>

            <!-- Visibility -->
            <div>
                <flux:heading size="lg" class="mb-4">Visibility</flux:heading>
                <flux:separator variant="subtle" class="mb-4" />
                <flux:field variant="inline">
                    <flux:checkbox wire:model="is_shared" />
                    <div>
                        <flux:label>Share this contact with other users</flux:label>
                        <flux:description>Personal contacts are only visible to you. Shared contacts can be seen by all users.</flux:description>
                    </div>
                </flux:field>
            </div>

            <!-- Tags -->
            <div>
                <flux:heading size="lg" class="mb-4">Tags</flux:heading>
                <flux:separator variant="subtle" class="mb-4" />
                <div class="flex flex-wrap gap-3">
                    @foreach($tags as $tag)
                        <label class="inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                wire:model="selectedTags" 
                                value="{{ $tag->id }}" 
                                class="sr-only peer"
                            >
                            <flux:badge 
                                variant="pill"
                                class="border-2 transition-all cursor-pointer peer-checked:border-current"
                                style="color: {{ $tag->color }}; background-color: {{ in_array($tag->id, $selectedTags) ? $tag->color . '20' : 'transparent' }}; border-color: {{ $tag->color }}"
                            >
                                {{ $tag->name }}
                            </flux:badge>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Notes -->
            <div>
                <flux:heading size="lg" class="mb-4">Notes</flux:heading>
                <flux:separator variant="subtle" class="mb-4" />
                <flux:field>
                    <flux:label>Additional Notes</flux:label>
                    <flux:textarea wire:model="notes" rows="6" placeholder="Add any additional notes about this contact..." />
                    <flux:error name="notes" />
                </flux:field>
            </div>

            <!-- Actions -->
            <flux:separator variant="subtle" />
            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('contacts.index') }}" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $contactId ? 'Update Contact' : 'Create Contact' }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>