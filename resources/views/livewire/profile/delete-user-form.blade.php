<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';
    public bool $showDeleteModal = false;

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }

    public function openDeleteModal(): void
    {
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->reset('password');
    }
}; ?>

<div>
    <div class="mb-6">
        <flux:heading size="lg">Delete Account</flux:heading>
        <flux:subheading>Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.</flux:subheading>
    </div>

    <flux:button wire:click="openDeleteModal" variant="danger" icon="trash">
        Delete Account
    </flux:button>

    @if($showDeleteModal)
        <flux:modal wire:model="showDeleteModal">
            <form wire:submit="deleteUser" class="space-y-6">
                <div>
                    <flux:heading size="lg">Are you sure you want to delete your account?</flux:heading>
                    <flux:text class="mt-3">
                        Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
                    </flux:text>
                </div>

                <flux:field>
                    <flux:label for="password">Password</flux:label>
                    <flux:input 
                        wire:model="password" 
                        id="password" 
                        name="password" 
                        type="password" 
                        placeholder="Enter your password" 
                        viewable
                    />
                    <flux:error name="password" />
                </flux:field>

                <div class="flex gap-2 justify-end">
                    <flux:button type="button" wire:click="closeDeleteModal" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="danger">
                        Delete Account
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>