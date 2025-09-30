<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <flux:button href="{{ route('contacts.index') }}" icon="arrow-left" variant="ghost">
            Back to Contacts
        </flux:button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            {{ $contactId ? 'Edit Contact' : 'Create Contact' }}
        </h1>

        <form wire:submit="save">
            <!-- Basic Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="first_name" label="First Name" required />
                    <flux:input wire:model="last_name" label="Last Name" required />
                    <flux:input wire:model="email" type="email" label="Email" />
                    <flux:input wire:model="phone_number" label="Phone Number" />
                    <flux:input wire:model="date_of_birth" type="date" label="Date of Birth" />
                    <flux:input wire:model="anniversary_date" type="date" label="Anniversary Date" />
                </div>
            </div>

            <!-- Photo Upload -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Photo</h2>
                
                @if($existingPhoto && !$photo)
                    <div class="mb-4">
                        <img src="{{ Storage::url($existingPhoto) }}" alt="Current photo" class="w-32 h-32 rounded-full object-cover">
                    </div>
                @endif

                @if($photo)
                    <div class="mb-4">
                        <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="w-32 h-32 rounded-full object-cover">
                    </div>
                @endif

                <flux:input wire:model="photo" type="file" label="Upload New Photo" accept="image/*" />
                <p class="mt-1 text-sm text-gray-500">Maximum file size: 2MB</p>
            </div>

            <!-- Address -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Address</h2>
                
                @if(!$createNewAddress && $addresses->isNotEmpty())
                    <div class="mb-4">
                        <flux:select wire:model.live="address_id" label="Select Existing Address">
                            <option value="">None</option>
                            @foreach($addresses as $address)
                                <option value="{{ $address->id }}">{{ $address->formatted_address }}</option>
                            @endforeach
                        </flux:select>
                        
                        <div class="mt-2">
                            <flux:checkbox wire:model.live="createNewAddress" label="Create new address" />
                        </div>
                    </div>
                @endif

                @if($createNewAddress || $addresses->isEmpty() || $address_id)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input wire:model="street" label="Street" class="md:col-span-2" />
                        <flux:input wire:model="city" label="City" />
                        <flux:input wire:model="postcode" label="Postcode" />
                        <flux:input wire:model="country" label="Country" class="md:col-span-2" />
                    </div>
                @endif
            </div>

            <!-- Tags -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tags</h2>
                <div class="flex flex-wrap gap-3">
                    @foreach($tags as $tag)
                        <label class="inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                wire:model="selectedTags" 
                                value="{{ $tag->id }}" 
                                class="sr-only peer"
                            >
                            <span class="px-4 py-2 rounded-full text-sm font-medium border-2 transition-all peer-checked:border-current"
                                  style="color: {{ $tag->color }}; background-color: {{ in_array($tag->id, $selectedTags) ? $tag->color . '20' : 'transparent' }}; border-color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h2>
                <flux:textarea wire:model="notes" label="Notes" rows="6" placeholder="Add any additional notes about this contact..." />
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('contacts.index') }}" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $contactId ? 'Update Contact' : 'Create Contact' }}
                </flux:button>
            </div>
        </form>
    </div>
</div>