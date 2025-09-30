<div class="p-6 max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $list ? 'Edit List' : 'Create List' }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">Group contacts for events, mailings, and more.</p>
        </div>
        <flux:button href="{{ $list ? route('lists.show', $list) : route('lists.index') }}" variant="ghost" icon="arrow-left">
            Back
        </flux:button>
    </div>

    @if (session('status'))
        <div class="rounded-lg border border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/30 p-4 text-sm text-green-700 dark:text-green-200">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="space-y-4">
            <flux:input label="List Name" wire:model.defer="name" required placeholder="Christmas Cards, Summer BBQ, VIP Clients..." />
            <flux:textarea label="Description" wire:model.defer="description" rows="4" placeholder="Optional description to remind you about this list." />
        </div>

        <div class="flex items-center justify-end gap-3">
            <flux:button href="{{ $list ? route('lists.show', $list) : route('lists.index') }}" variant="ghost">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary">
                {{ $list ? 'Save Changes' : 'Create List' }}
            </flux:button>
        </div>
    </form>
</div>
