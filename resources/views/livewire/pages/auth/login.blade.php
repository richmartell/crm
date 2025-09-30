<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        if (Auth::user()?->two_factor_enabled) {
            $this->redirectRoute('two-factor.verify');
            return;
        }

        Session::regenerate();

        $this->redirectIntended(default: route('contacts.index', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            {{ session('status') }}
        </flux:callout>
    @endif

    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <flux:field>
            <flux:label for="email">Email</flux:label>
            <flux:input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username" />
            <flux:error name="form.email" />
        </flux:field>

        <!-- Password -->
        <flux:field>
            <flux:label for="password">Password</flux:label>
            <flux:input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password" />
            <flux:error name="form.password" />
        </flux:field>

        <!-- Remember Me -->
        <flux:field variant="inline">
            <flux:checkbox wire:model="form.remember" id="remember" name="remember" />
            <flux:label for="remember">Remember me</flux:label>
        </flux:field>

        <div class="flex items-center justify-between pt-4">
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300" href="{{ route('password.request') }}" wire:navigate>
                    Forgot your password?
                </a>
            @endif

            <flux:button type="submit" variant="primary">
                Log in
            </flux:button>
        </div>
    </form>
</div>
