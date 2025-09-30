<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        session()->flash('profile-updated', true);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('contacts.index', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        session()->flash('verification-link-sent', true);
    }
}; ?>

<div>
    <div class="mb-6">
        <flux:heading size="lg">Profile Information</flux:heading>
        <flux:subheading>Update your account's profile information and email address.</flux:subheading>
    </div>

    <form wire:submit="updateProfileInformation" class="space-y-6">
        <flux:field>
            <flux:label for="name">Name</flux:label>
            <flux:input wire:model="name" id="name" name="name" type="text" required autofocus autocomplete="name" />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label for="email">Email</flux:label>
            <flux:input wire:model="email" id="email" name="email" type="email" required autocomplete="username" />
            <flux:error name="email" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-3">
                    <flux:callout variant="warning" icon="exclamation-triangle">
                        <div>
                            Your email address is unverified.
                            <flux:button wire:click.prevent="sendVerification" variant="ghost" size="sm" class="mt-2">
                                Click here to re-send the verification email.
                            </flux:button>
                        </div>
                    </flux:callout>

                    @if (session('verification-link-sent'))
                        <flux:callout variant="success" icon="check-circle" class="mt-2">
                            A new verification link has been sent to your email address.
                        </flux:callout>
                    @endif
                </div>
            @endif
        </flux:field>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">Save</flux:button>

            @if (session('profile-updated'))
                <flux:text class="text-green-600 dark:text-green-400">Saved.</flux:text>
            @endif
        </div>
    </form>
</div>