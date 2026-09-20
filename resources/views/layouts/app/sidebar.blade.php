<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50/70 dark:bg-zinc-950">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200/70 bg-white dark:border-white/10 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('admin.dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('admin.nav_group_main')" class="grid">
                    <flux:sidebar.item icon="layout-grid" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>
                        {{ __('admin.nav_dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="archive-box" :href="route('admin.shipments.index')" :current="request()->routeIs('admin.shipments.*')" wire:navigate>
                        {{ __('admin.nav_shipments') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="plus-circle" :href="route('admin.shipments.create')" :current="request()->routeIs('admin.shipments.create')" wire:navigate>
                        {{ __('admin.nav_create_shipment') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('admin.recipients.index')" :current="request()->routeIs('admin.recipients.*')" wire:navigate>
                        {{ __('admin.nav_recipients') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user" :href="route('admin.senders.index')" :current="request()->routeIs('admin.senders.*')" wire:navigate>
                        {{ __('admin.nav_senders') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="map-pin" :href="route('admin.tracking-events.index')" :current="request()->routeIs('admin.tracking-events.*')" wire:navigate>
                        {{ __('admin.nav_events') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="bell" :href="route('admin.notifications.index')" :current="request()->routeIs('admin.notifications.*')" wire:navigate>
                        {{ __('admin.nav_notifications') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @if (auth()->user()->isAdmin())
                    <flux:sidebar.group :heading="__('admin.nav_group_admin')" class="grid">
                        <flux:sidebar.item icon="identification" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')" wire:navigate>
                            {{ __('admin.nav_users') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="cog-6-tooth" :href="route('admin.settings.edit')" :current="request()->routeIs('admin.settings.*')" wire:navigate>
                            {{ __('admin.nav_settings') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="globe-alt" :href="route('home')" wire:navigate>
                    {{ __('admin.nav_public_site') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
