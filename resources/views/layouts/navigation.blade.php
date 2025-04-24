<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 fixed inset-x-0 top-0 z-50 h-16">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dpe.index') }}">
                        <x-application-logo class="block h-10 w-auto fill-current text-gray-600" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dpe.index')" :active="request()->routeIs('dpe.index')">
                        {{ __('DPE Data') }}
                    </x-nav-link>
                </div>
            </div>
        </div>
    </div>
</nav>
