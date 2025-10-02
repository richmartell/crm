<div class="py-6 space-y-6">
    <div>
        <flux:heading size="xl">Birthdays & Anniversaries</flux:heading>
        <flux:subheading>Upcoming celebrations in the next year</flux:subheading>
    </div>

    @if($events->isEmpty())
        <flux:card class="text-center py-12">
            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 mb-4">
                <flux:icon.cake class="size-6 text-blue-600 dark:text-blue-300" />
            </div>
            <flux:heading size="lg">No upcoming events</flux:heading>
            <flux:subheading>No birthdays or anniversaries in the next year.</flux:subheading>
        </flux:card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $currentMonth = null;
            @endphp
            
            @foreach($events as $event)
                @php
                    $eventMonth = $event->upcoming_date->format('F Y');
                @endphp
                
                @if($currentMonth !== $eventMonth)
                    @php
                        $currentMonth = $eventMonth;
                    @endphp
                    <div class="md:col-span-2 lg:col-span-3">
                        <flux:heading size="lg" class="flex items-center gap-2 mt-4 mb-2">
                            <flux:icon.calendar class="size-5" />
                            {{ $eventMonth }}
                        </flux:heading>
                        <flux:separator variant="subtle" />
                    </div>
                @endif

                <flux:card class="hover:shadow-lg transition-shadow {{ $event->is_today ? 'ring-2 ring-blue-500' : '' }}">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            @if($event->event_type === 'birthday')
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-2xl">🎉</span>
                                </div>
                            @else
                                {{-- Anniversary - show heart icon --}}
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-pink-500 to-red-600 flex items-center justify-center">
                                    <flux:icon.heart class="size-6 text-white" variant="solid" />
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            @if($event->event_type === 'birthday')
                                <a href="{{ route('contacts.show', $event) }}" class="block font-semibold text-zinc-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors truncate text-lg">
                                    {{ $event->full_name }}
                                </a>
                                
                                <div class="flex items-center gap-2 mt-1">
                                    <flux:icon.cake class="size-4 text-blue-600 dark:text-blue-400" />
                                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                        {{ $event->upcoming_date->format('F jS') }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center gap-2 mt-1">
                                    <flux:icon.gift class="size-4 text-purple-600 dark:text-purple-400" />
                                    <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                        Turning {{ $event->upcoming_age }} years old
                                    </span>
                                </div>
                            @else
                                {{-- Anniversary --}}
                                <div class="font-semibold text-zinc-900 dark:text-white text-lg">
                                    @if($event->spouse && $event->last_name === $event->spouse->last_name)
                                        {{-- Same surname - show "FirstName1 and FirstName2 Surname" --}}
                                        <a href="{{ route('contacts.show', $event) }}" class="hover:text-pink-600 dark:hover:text-pink-400 transition-colors">
                                            {{ $event->first_name }}
                                        </a>
                                        <span class="text-zinc-500 dark:text-zinc-400"> and </span>
                                        <a href="{{ route('contacts.show', $event->spouse) }}" class="hover:text-pink-600 dark:hover:text-pink-400 transition-colors">
                                            {{ $event->spouse->first_name }}
                                        </a>
                                        <span> {{ $event->last_name }}</span>
                                    @else
                                        {{-- Different surnames or no spouse - show full names --}}
                                        <a href="{{ route('contacts.show', $event) }}" class="hover:text-pink-600 dark:hover:text-pink-400 transition-colors">
                                            {{ $event->full_name }}
                                        </a>
                                        @if($event->spouse)
                                            <span class="text-zinc-500 dark:text-zinc-400"> & </span>
                                            <a href="{{ route('contacts.show', $event->spouse) }}" class="hover:text-pink-600 dark:hover:text-pink-400 transition-colors">
                                                {{ $event->spouse->full_name }}
                                            </a>
                                        @endif
                                    @endif
                                </div>
                                
                                <div class="flex items-center gap-2 mt-1">
                                    <flux:icon.heart class="size-4 text-pink-600 dark:text-pink-400" variant="solid" />
                                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                        {{ $event->upcoming_date->format('F jS') }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center gap-2 mt-1">
                                    <flux:icon.sparkles class="size-4 text-pink-600 dark:text-pink-400" />
                                    <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $event->upcoming_years }} {{ Str::plural('year', $event->upcoming_years) }} together
                                    </span>
                                </div>
                            @endif
                            
                            <div class="mt-2">
                                @if($event->is_today)
                                    <flux:badge color="blue" size="sm" icon="sparkles">
                                        Today! 🎉
                                    </flux:badge>
                                @elseif($event->days_until == 1)
                                    <flux:badge color="green" size="sm">
                                        Tomorrow
                                    </flux:badge>
                                @elseif($event->days_until <= 7)
                                    <flux:badge color="yellow" size="sm">
                                        In {{ $event->days_until }} days
                                    </flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">
                                        In {{ $event->days_until }} days
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
