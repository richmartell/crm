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
                
                <div class="mb-4">
                    @if($photo)
                        <flux:avatar src="{{ $photo->temporaryUrl() }}" size="xl" />
                        <flux:text size="sm" class="mt-2">New photo preview</flux:text>
                    @elseif($existingPhoto)
                        <flux:avatar src="{{ Storage::url($existingPhoto) }}" size="xl" />
                        <flux:text size="sm" class="mt-2">Current photo</flux:text>
                    @elseif($email)
                        @php
                            $gravatarHash = md5(strtolower(trim($email)));
                            $gravatarUrl = "https://www.gravatar.com/avatar/{$gravatarHash}?d=mp&s=200";
                        @endphp
                        <flux:avatar src="{{ $gravatarUrl }}" size="xl" />
                        <flux:text size="sm" class="mt-2">Gravatar from email</flux:text>
                    @else
                        <flux:avatar size="xl" class="bg-gradient-to-br from-blue-500 to-purple-600">
                            <span class="text-2xl font-bold text-white">
                                {{ $first_name ? substr($first_name, 0, 1) : '' }}{{ $last_name ? substr($last_name, 0, 1) : '' }}
                            </span>
                        </flux:avatar>
                        <flux:text size="sm" class="mt-2">Default avatar</flux:text>
                    @endif
                </div>

                <flux:field>
                    <flux:label>Upload New Photo</flux:label>
                    <flux:input wire:model="photo" type="file" accept="image/*" />
                    <flux:description>Upload a photo or we'll use your Gravatar if you have one set up with your email address.</flux:description>
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
                            <flux:label>Address Line 1</flux:label>
                            <flux:input wire:model="street" />
                            <flux:error name="street" />
                        </flux:field>
                        
                        <flux:field class="md:col-span-2">
                            <flux:label>Address Line 2</flux:label>
                            <flux:input wire:model="address_line_2" placeholder="Apartment, suite, etc. (optional)" />
                            <flux:error name="address_line_2" />
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
                            <flux:select wire:model="country">
                                <option value="">Select a country...</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="United States">United States</option>
                                <option value="Canada">Canada</option>
                                <option value="Australia">Australia</option>
                                <option value="Ireland">Ireland</option>
                                <option value="New Zealand">New Zealand</option>
                                <option value="France">France</option>
                                <option value="Germany">Germany</option>
                                <option value="Spain">Spain</option>
                                <option value="Italy">Italy</option>
                                <option value="Netherlands">Netherlands</option>
                                <option value="Belgium">Belgium</option>
                                <option value="Switzerland">Switzerland</option>
                                <option value="Austria">Austria</option>
                                <option value="Portugal">Portugal</option>
                                <option value="Sweden">Sweden</option>
                                <option value="Norway">Norway</option>
                                <option value="Denmark">Denmark</option>
                                <option value="Finland">Finland</option>
                                <option value="Poland">Poland</option>
                                <option value="Other">Other</option>
                            </flux:select>
                            <flux:error name="country" />
                        </flux:field>
                    </div>
                @endif
            </div>

            <!-- Visibility -->
            <div>
                <flux:heading size="lg" class="mb-4">Visibility</flux:heading>
                <flux:separator variant="subtle" class="mb-4" />
                <div class="flex items-start gap-3">
                    <flux:checkbox wire:model="is_shared" id="is_shared" class="mt-1" />
                    <div class="flex-1">
                        <label for="is_shared" class="text-sm font-medium text-zinc-900 dark:text-white cursor-pointer">
                            Share this contact with other users
                        </label>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                            Personal contacts are only visible to you. Shared contacts can be seen by all users.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tags -->
            <div>
                <flux:heading size="lg" class="mb-4">Tags</flux:heading>
                <flux:separator variant="subtle" class="mb-4" />
                <div class="flex flex-wrap gap-3">
                    @foreach($tags as $tag)
                        <label class="cursor-pointer">
                            <input 
                                type="checkbox" 
                                wire:model.live="selectedTags" 
                                value="{{ $tag->id }}" 
                                class="sr-only peer"
                            />
                            <span 
                                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border-2 transition-all peer-checked:ring-2 peer-checked:ring-offset-2"
                                style="color: {{ $tag->color }}; background-color: {{ in_array($tag->id, $selectedTags) ? $tag->color . '20' : 'transparent' }}; border-color: {{ $tag->color }}; ring-color: {{ $tag->color }};"
                            >
                                {{ $tag->name }}
                            </span>
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