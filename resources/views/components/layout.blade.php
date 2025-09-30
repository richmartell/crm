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
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
    @auth
        <flux:header container class="bg-white dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800">
            <flux:brand href="{{ route('contacts.index') }}" name="Personal CRM" class="max-lg:!hidden">
                <flux:icon.users class="size-6" variant="solid" />
            </flux:brand>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item 
                    icon="users" 
                    href="{{ route('contacts.index') }}" 
                    :current="request()->routeIs('contacts.*')"
                >
                    Contacts
                </flux:navbar.item>
                <flux:navbar.item 
                    icon="clipboard-document-list" 
                    href="{{ route('lists.index') }}"
                    :current="request()->routeIs('lists.*')"
                >
                    Lists
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            @php
                $user = Auth::user();
                $avatar = sprintf('https://www.gravatar.com/avatar/%s?d=mp', md5(strtolower(trim($user->email))));
            @endphp

            <flux:dropdown position="top" align="start">
                <flux:profile avatar="{{ $avatar }}" name="{{ $user->name }}" />
                
                <flux:menu>
                    <flux:menu.heading>{{ $user->name }}</flux:menu.heading>
                    <flux:menu.item icon="user" href="{{ route('profile.edit') }}">Profile</flux:menu.item>
                    <flux:menu.item icon="shield-check" href="{{ route('two-factor.settings') }}">Two-Factor Auth</flux:menu.item>
                    
                    <flux:menu.separator />
                    
                    <flux:menu.item icon="clipboard-document-list" href="{{ route('lists.index') }}">My Lists</flux:menu.item>
                    
                    <flux:menu.separator />
                    
                    <flux:menu.item 
                        icon="arrow-right-start-on-rectangle"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    >
                        Logout
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </flux:header>

        <flux:sidebar sticky collapsible="mobile" class="lg:hidden bg-white dark:bg-zinc-950 border-r border-zinc-200 dark:border-zinc-800">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            
            <flux:sidebar.header>
                <flux:sidebar.brand href="{{ route('contacts.index') }}" name="Personal CRM">
                    <flux:icon.users class="size-6" variant="solid" />
                </flux:sidebar.brand>
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.item 
                    icon="users" 
                    href="{{ route('contacts.index') }}" 
                    :current="request()->routeIs('contacts.*')"
                >
                    Contacts
                </flux:sidebar.item>
                <flux:sidebar.item 
                    icon="clipboard-document-list" 
                    href="{{ route('lists.index') }}"
                    :current="request()->routeIs('lists.*')"
                >
                    Lists
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="user" href="{{ route('profile.edit') }}">Profile</flux:sidebar.item>
                <flux:sidebar.item icon="shield-check" href="{{ route('two-factor.settings') }}">Settings</flux:sidebar.item>
            </flux:sidebar.nav>
        </flux:sidebar>
    @endauth

    <flux:main container>
        {{ $slot }}
    </flux:main>
    
    @fluxScripts
</body>
</html>
