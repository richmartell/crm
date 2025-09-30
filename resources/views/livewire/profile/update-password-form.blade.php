<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        session()->flash('password-updated', true);
    }
}; ?>

<div>
    <div class="mb-6">
        <flux:heading size="lg">Update Password</flux:heading>
        <flux:subheading>Ensure your account is using a long, random password to stay secure.</flux:subheading>
    </div>

    <form wire:submit="updatePassword" class="space-y-6">
        <flux:field>
            <flux:label for="update_password_current_password">Current Password</flux:label>
            <flux:input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" viewable />
            <flux:error name="current_password" />
        </flux:field>

        <flux:field>
            <flux:label for="update_password_password">New Password</flux:label>
            <flux:input wire:model="password" id="update_password_password" name="password" type="password" autocomplete="new-password" viewable />
            <flux:error name="password" />
        </flux:field>

        <flux:field>
            <flux:label for="update_password_password_confirmation">Confirm Password</flux:label>
            <flux:input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" viewable />
            <flux:error name="password_confirmation" />
        </flux:field>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">Save</flux:button>

            @if (session('password-updated'))
                <flux:text class="text-green-600 dark:text-green-400">Saved.</flux:text>
            @endif
        </div>
    </form>
</div>