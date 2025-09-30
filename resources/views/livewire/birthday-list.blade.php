<div class="py-6 space-y-6">
    <div>
        <flux:heading size="xl">Upcoming Birthdays</flux:heading>
        <flux:subheading>Birthdays in the next 3 months</flux:subheading>
    </div>

    @if($birthdays->isEmpty())
        <flux:card class="text-center py-12">
            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 mb-4">
                <flux:icon.cake class="size-6 text-blue-600 dark:text-blue-300" />
            </div>
            <flux:heading size="lg">No upcoming birthdays</flux:heading>
            <flux:subheading>No contacts with birthdays in the next 3 months.</flux:subheading>
        </flux:card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $currentMonth = null;
            @endphp
            
            @foreach($birthdays as $contact)
                @php
                    $birthdayMonth = $contact->upcoming_birthday->format('F Y');
                @endphp
                
                @if($currentMonth !== $birthdayMonth)
                    @php
                        $currentMonth = $birthdayMonth;
                    @endphp
                    <div class="md:col-span-2 lg:col-span-3">
                        <flux:heading size="lg" class="flex items-center gap-2 mt-4 mb-2">
                            <flux:icon.calendar class="size-5" />
                            {{ $birthdayMonth }}
                        </flux:heading>
                        <flux:separator variant="subtle" />
                    </div>
                @endif

                <flux:card class="hover:shadow-lg transition-shadow {{ $contact->is_today ? 'ring-2 ring-blue-500' : '' }}">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            @if($contact->avatar_url)
                                <flux:avatar src="{{ $contact->avatar_url }}" size="lg" />
                            @else
                                <flux:avatar size="lg" class="bg-gradient-to-br from-blue-500 to-purple-600">
                                    <span class="text-xl font-bold text-white">
                                        {{ $contact->initials }}
                                    </span>
                                </flux:avatar>
                            @endif
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('contacts.show', $contact) }}" class="block font-semibold text-zinc-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors truncate text-lg">
                                {{ $contact->full_name }}
                            </a>
                            
                            <div class="flex items-center gap-2 mt-1">
                                <flux:icon.cake class="size-4 text-blue-600 dark:text-blue-400" />
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                    {{ $contact->upcoming_birthday->format('F jS') }}
                                </span>
                            </div>
                            
                            <div class="flex items-center gap-2 mt-1">
                                <flux:icon.gift class="size-4 text-purple-600 dark:text-purple-400" />
                                <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                    Turning {{ $contact->upcoming_age }} years old
                                </span>
                            </div>
                            
                            <div class="mt-2">
                                @if($contact->is_today)
                                    <flux:badge color="blue" size="sm" icon="sparkles">
                                        Today! 🎉
                                    </flux:badge>
                                @elseif($contact->days_until == 1)
                                    <flux:badge color="green" size="sm">
                                        Tomorrow
                                    </flux:badge>
                                @elseif($contact->days_until <= 7)
                                    <flux:badge color="yellow" size="sm">
                                        In {{ $contact->days_until }} days
                                    </flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">
                                        In {{ $contact->days_until }} days
                                    </flux:badge>
                                @endif
                            </div>
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif
</div>
