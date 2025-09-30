<div class="py-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ $list ? 'Edit List' : 'Create List' }}</flux:heading>
            <flux:subheading>Group contacts for events, mailings, and more.</flux:subheading>
        </div>
        <flux:button href="{{ $list ? route('lists.show', $list) : route('lists.index') }}" variant="ghost" icon="arrow-left">
            Back
        </flux:button>
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('status') }}
        </flux:callout>
    @endif

    <flux:card>
        <form wire:submit.prevent="save" class="space-y-6">
            <flux:field>
                <flux:label>List Name</flux:label>
                <flux:input wire:model.defer="name" required placeholder="Christmas Cards, Summer BBQ, VIP Clients..." />
                <flux:error name="name" />
            </flux:field>
            
            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea wire:model.defer="description" rows="4" placeholder="Optional description to remind you about this list." />
                <flux:description>Help you remember what this list is for later.</flux:description>
                <flux:error name="description" />
            </flux:field>

            <flux:separator variant="subtle" />

            <div class="flex items-center justify-end gap-3">
                <flux:button href="{{ $list ? route('lists.show', $list) : route('lists.index') }}" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $list ? 'Save Changes' : 'Create List' }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
