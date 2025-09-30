<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Personal CRM' }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="bg-gray-50 dark:bg-gray-950 min-h-screen">
    <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('contacts.index') }}" class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">Personal CRM</span>
                    </a>
                </div>
                <div class="flex items-center gap-6">
                    @auth
                        <div class="hidden md:flex items-center gap-6 text-sm font-semibold text-gray-600 dark:text-gray-300">
                            <a href="{{ route('contacts.index') }}" class="{{ request()->routeIs('contacts.*') ? 'text-blue-600 dark:text-blue-400' : '' }} hover:text-blue-600 dark:hover:text-blue-400">
                                Contacts
                            </a>
                            <a href="{{ route('lists.index') }}" class="{{ request()->routeIs('lists.*') ? 'text-blue-600 dark:text-blue-400' : '' }} hover:text-blue-600 dark:hover:text-blue-400">
                                Lists
                            </a>
                        </div>
                        <div class="md:hidden">
                            <flux:dropdown position="bottom" align="start">
                                <flux:button variant="ghost" icon="bars-3">
                                    Menu
                                </flux:button>
                                <flux:menu>
                                    <flux:menu.item href="{{ route('contacts.index') }}" icon="users">Contacts</flux:menu.item>
                                    <flux:menu.item href="{{ route('lists.index') }}" icon="clipboard-document-list">Lists</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    @endauth
                    
                    @auth
                        @php
                            $user = Auth::user();
                            $avatar = sprintf('https://www.gravatar.com/avatar/%s?d=mp', md5(strtolower(trim($user->email))));
                        @endphp
        
                        <flux:dropdown align="end">
                            <flux:profile avatar="{{ $avatar }}" />

                            <flux:navmenu class="max-w-[14rem]">
                                <div class="px-2 py-1.5">
                                    <flux:text size="sm">Signed in as</flux:text>
                                    <flux:heading class="mt-1! truncate">{{ $user->name }}</flux:heading>
                                    <flux:text size="sm" class="mt-0.5 truncate text-zinc-500 dark:text-zinc-400">{{ $user->email }}</flux:text>
                                </div>

                                <flux:navmenu.separator />

                                <flux:navmenu.item href="{{ route('profile.edit') }}" icon="user" class="text-zinc-800 dark:text-white">
                                    Profile
                                </flux:navmenu.item>
                                <flux:navmenu.item href="{{ route('two-factor.settings') }}" icon="shield-check" class="text-zinc-800 dark:text-white">
                                    Two-Factor Auth
                                </flux:navmenu.item>

                                <flux:navmenu.separator />

                                <flux:navmenu.item
                                    href="{{ route('lists.index') }}"
                                    icon="clipboard-document-list"
                                    class="text-zinc-800 dark:text-white"
                                >
                                    My Lists
                                </flux:navmenu.item>

                                <flux:navmenu.separator />

                                <flux:navmenu.item
                                    href="{{ route('logout') }}"
                                    icon="arrow-right-start-on-rectangle"
                                    class="text-zinc-800 dark:text-white"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                >
                                    Logout
                                </flux:navmenu.item>
                            </flux:navmenu>
                        </flux:dropdown>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>
    @fluxScripts
</body>
</html>
