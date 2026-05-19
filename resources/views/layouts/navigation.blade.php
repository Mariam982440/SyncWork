@php
    $user = Auth::user();
    $roleLabel = match($user->role) {
        'admin' => 'Admin',
        'rh' => 'RH',
        default => 'Employe',
    };
    $initials = collect(explode(' ', trim($user->name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->join('');

    $links = [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'active' => request()->routeIs('dashboard'),
            'icon' => 'dashboard',
            'show' => true,
        ],
        [
            'label' => 'Employes',
            'route' => 'employees.index',
            'active' => request()->routeIs('employees.*'),
            'icon' => 'users',
            'show' => $user->hasRole(['admin', 'rh']),
        ],
        [
            'label' => 'Conges',
            'route' => 'conges.index',
            'active' => request()->routeIs('conges.*'),
            'icon' => 'calendar',
            'show' => true,
        ],
    ];
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
            <div class="flex items-center gap-7">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-700 text-white shadow-sm">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 9.75 12 7.5l4 2.25M8 14.25l4 2.25 4-2.25" />
                        </svg>
                    </span>
                    <span class="leading-tight">
                        <span class="block text-base font-bold tracking-tight text-slate-900">SyncWork</span>
                        <span class="block text-xs font-medium text-slate-400">People operations</span>
                    </span>
                </a>

                <div class="hidden items-center gap-1 md:flex">
                    @foreach($links as $link)
                        @if($link['show'])
                            <a href="{{ route($link['route']) }}"
                               class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium transition
                                      {{ $link['active'] ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                @if($link['icon'] === 'dashboard')
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5h6.75V3.75H3.75v9.75ZM13.5 20.25h6.75V10.5H13.5v9.75ZM3.75 20.25h6.75v-3.75H3.75v3.75ZM13.5 6.75h6.75v-3H13.5v3Z" />
                                    </svg>
                                @elseif($link['icon'] === 'users')
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.125a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766v-.109Z" />
                                    </svg>
                                @else
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M5.25 5.25h13.5A1.5 1.5 0 0 1 20.25 6.75v12A1.5 1.5 0 0 1 18.75 20.25H5.25A1.5 1.5 0 0 1 3.75 18.75v-12A1.5 1.5 0 0 1 5.25 5.25Z" />
                                    </svg>
                                @endif
                                {{ $link['label'] }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                    {{ $roleLabel }}
                </span>

                <x-dropdown align="right" width="56" contentClasses="py-2 bg-white">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-2.5 py-2 text-left shadow-sm transition hover:border-blue-200 hover:bg-blue-50/40 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900 text-sm font-semibold text-white">
                                {{ $initials ?: 'SW' }}
                            </span>
                            <span class="hidden min-w-0 lg:block">
                                <span class="block max-w-36 truncate text-sm font-semibold text-slate-800">{{ $user->name }}</span>
                                <span class="block max-w-36 truncate text-xs text-slate-400">{{ $user->email }}</span>
                            </span>
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                            <p class="truncate text-xs text-slate-400">{{ $user->email }}</p>
                        </div>
                        <div class="my-1 border-t border-slate-100"></div>
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Deconnexion
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <button @click="open = ! open"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-50 hover:text-slate-900 md:hidden">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-100 bg-white md:hidden">
        <div class="space-y-1 px-4 py-3">
            @foreach($links as $link)
                @if($link['show'])
                    <a href="{{ route($link['route']) }}"
                       class="block rounded-xl px-3 py-2 text-sm font-medium
                              {{ $link['active'] ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        {{ $link['label'] }}
                    </a>
                @endif
            @endforeach
        </div>

        <div class="border-t border-slate-100 px-4 py-4">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-sm font-semibold text-white">
                    {{ $initials ?: 'SW' }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                    <p class="truncate text-xs text-slate-400">{{ $user->email }}</p>
                </div>
                <span class="ml-auto rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                    {{ $roleLabel }}
                </span>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); this.closest('form').submit();"
                       class="block rounded-xl px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                        Deconnexion
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
