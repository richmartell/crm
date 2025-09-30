<x-layout>
    <div class="py-6 space-y-6 max-w-4xl mx-auto">
        <div>
            <flux:heading size="xl">Profile Settings</flux:heading>
            <flux:subheading>Manage your account information and security settings.</flux:subheading>
        </div>

        <flux:card>
            <livewire:profile.update-profile-information-form />
        </flux:card>

        <flux:card>
            <livewire:profile.update-password-form />
        </flux:card>

        <flux:card>
            <livewire:profile.delete-user-form />
        </flux:card>
    </div>
</x-layout>