<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


@php
$user = Auth::user();
$role = $user->role ?? 'guest';

use App\Models\Veterinarian;

// Check if user is veterinarian admin
$isVetAdmin = false;
if ($role === 'veterinarian') {
    $vet = $user->veterinarian ?? Veterinarian::where('user_id', $user->id)->first();
    $isVetAdmin = $vet && $vet->is_admin;
}

$navigation = [
    [
        'name' => 'Dashboard',
        'href' => match($role) {
            'admin' => route('admin.dashboard'),
            'veterinarian' => route('vet.dashboard'),
            'owner' => route('owner.dashboard'),
            default => '#'
        },
        'icon' => 'heroicon-o-home',
        'active' => in_array(request()->route()->getName(), ['admin.dashboard', 'vet.dashboard', 'owner.dashboard']),
        'roles' => ['admin', 'veterinarian', 'owner']
    ],
    [
        'name' => 'Appointments',
        'href' => route('appointments.index'),
        'icon' => 'heroicon-o-calendar',
        'active' => request()->routeIs('appointments.*'),
        'roles' => ['admin', 'veterinarian', 'owner']
    ],
    // Admin-specific items
    [
        'name' => 'User Management',
        'href' => route('admin.users.index'),
        'icon' => 'heroicon-o-user-group',
        'active' => request()->routeIs('admin.users.*'),
        'roles' => ['admin']
    ],
    [
        'name' => 'Veterinarians',
        'href' => route('admin.veterinarians.index'),
        'icon' => 'heroicon-o-user-add',
        'active' => request()->routeIs('admin.veterinarians.*'),
        'roles' => ['admin']
    ],
    // Veterinarian-specific items
    [
        'name' => 'Medical Records',
        'href' => Auth::user()->role === 'veterinarian'
            ? route('vet.medrecords.index')
            : route('medical-records.index'),
        'icon' => 'heroicon-o-document-text',
        'active' => request()->routeIs('vet.medrecords.*') ||
                    request()->routeIs('medical-records.*'),
        'roles' => ['veterinarian', 'owner', 'admin'],
        'vetAdmin' => true
    ],
    [
        'name' => 'Clinic Settings',
        'href' => route('vet.settings'),
        'icon' => 'heroicon-o-cog',
        'active' => request()->routeIs('vet.settings'),
        'roles' => ['veterinarian'],
        'vetAdmin' => true
    ],

    [
    'name' => 'Chat',
    'href' => route('chat.index'),
    'icon' => 'heroicon-o-chat-bubble-left-ellipsis',
    'active' => request()->routeIs('chat.*'),
    'roles' => ['veterinarian', 'owner']
    ],

    // Owner-specific items
    [
        'name' => 'My Pets',
        'href' => route('owner.pets.index'),
        'icon' => 'heroicon-o-paw',
        'active' => request()->routeIs('owner.pets.*'),
        'roles' => ['owner']
    ],
];

// Filter navigation items based on user role
// $filteredNavigation = array_filter($navigation, function($item) use ($role) {
//     return in_array($role, $item['roles']);
// });

// Filter navigation based on role AND vet admin status
$filteredNavigation = array_filter($navigation, function($item) use ($role, $isVetAdmin) {
    $allowedByRole = in_array($role, $item['roles']);
    $allowedByVetAdmin = ($isVetAdmin && ($item['vetAdmin'] ?? false));
    return $allowedByRole || $allowedByVetAdmin;
});

// Count overdue scheduled appointments for vet
$overdueCount = 0;
if ($role === 'veterinarian') {
    $overdueCount = \App\Models\Appointment::where('veterinarian_id', $user->veterinarian->id)
        ->where('status', 'Scheduled')
        ->where('appointment_date', '<', now())
        ->count();
}
@endphp

<div x-data="{}" class="fixed inset-y-0 left-0 z-50">
    <!-- Mobile overlay -->
    <div x-show="$store.sidebar.expanded && window.innerWidth < 1024"
        x-transition.opacity
        @click="$store.sidebar.expanded = false"
        class="fixed inset-0 bg-black bg-opacity-50 lg:hidden"
        style="display: none">
    </div>

    <div
        :class="{
            'w-64': $store.sidebar.expanded,
            'w-20': !$store.sidebar.expanded,
            'fixed inset-y-0 left-0 z-50': window.innerWidth < 1024 // always fixed on mobile
        }"
        class="bg-gray-800 text-white h-screen transition-all duration-300 transform flex flex-col"
        x-show="$store.sidebar.expanded || window.innerWidth >= 1024"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        style="display: none"
    >
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-700">
            <div class="flex items-center" x-show="$store.sidebar.expanded">
                <x-application-logo class="block h-9 w-auto text-white"/>
                <span class="ml-3 text-xl font-semibold">VRMS</span>
            </div>
            <!-- Desktop toggle button only -->
            <button @click="$store.sidebar.toggle()"
                    class="text-gray-400 hover:text-white focus:outline-none hidden lg:block">
                <svg :class="$store.sidebar.expanded ? '' : 'rotate-180'" class="h-6 w-6 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Fixed Navigation Links -->
        <nav class="flex-1 px-2 py-4 overflow-y-auto">
            @foreach ($filteredNavigation as $item)
            <a href="{{ $item['href'] }}"
                class="group flex items-center rounded-md mb-1 transition-colors"
                :class="{
                    'bg-gray-900 text-white': {{ $item['active'] ? 'true' : 'false' }},
                    'text-gray-300 hover:bg-gray-700 hover:text-white': {{ !$item['active'] ? 'true' : 'false' }},
                    'px-4 py-3 justify-start': $store.sidebar.expanded,
                    'px-3 py-4 justify-center': !$store.sidebar.expanded
                }"
            >
                <!-- Icon (dynamic based on $item['icon']) -->
                @switch($item['icon'])
                    @case('heroicon-o-home')
                        <!-- Home Icon -->
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        @break
                    @case('heroicon-o-calendar')
                        <!-- Calendar Icon -->
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        @break
                    @case('heroicon-o-user-group')
                        <!-- User Group Icon -->
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a4 4 0 00-3-3.87M9 20h6M3 20h5v-2a4 4 0 013-3.87M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        @break
                    @case('heroicon-o-user-add')
                        <!-- User Add Icon -->
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-6 4a4 4 0 100-8 4 4 0 000 8zm6 4v-1a4 4 0 00-3-3.87" />
                        </svg>
                        @break
                    @case('heroicon-o-document-text')
                        <!-- Document Text Icon -->
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 16h8M8 12h8M8 8h8M4 6a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" />
                        </svg>
                        @break
                    @case('heroicon-o-cog')
                        <!-- Cog Icon -->
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0a1.724 1.724 0 002.573.982c.797-.46 1.8.149 1.8 1.048v2.02a1.724 1.724 0 001.048 1.8c.921.3.921 1.603 0 1.902a1.724 1.724 0 00-.982 2.573c.46.797-.149 1.8-1.048 1.8h-2.02a1.724 1.724 0 00-1.8 1.048c-.3.921-1.603.921-1.902 0a1.724 1.724 0 00-2.573-.982c-.797.46-1.8-.149-1.8-1.048v-2.02a1.724 1.724 0 00-1.048-1.8c-.921-.3-.921-1.603 0-1.902a1.724 1.724 0 00.982-2.573c-.46-.797.149-1.8 1.048-1.8h2.02a1.724 1.724 0 001.8-1.048z" />
                        </svg>
                        @break
                    @case('heroicon-o-chat-bubble-left-ellipsis')
                        <!-- Chat Icon -->
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        @break
                    @case('heroicon-o-paw')
                        <!-- Paw Icon -->
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle cx="12" cy="12" r="3" />
                            <circle cx="19" cy="7" r="2" />
                            <circle cx="5" cy="7" r="2" />
                            <circle cx="7" cy="17" r="2" />
                            <circle cx="17" cy="17" r="2" />
                        </svg>
                        @break
                    @default
                        <!-- Default Icon -->
                        <svg class="flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" />
                        </svg>
                @endswitch
                <span :class="$store.sidebar.expanded ? 'ml-3' : 'hidden'" class="transition-all duration-200">
                    {{ $item['name'] }}
                </span>
                @if($item['name'] === 'Appointments' && $overdueCount > 0)
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-600 text-white">
                        {{ $overdueCount }}
                    </span>
                @endif
            </a>
            @endforeach
        </nav>

        <!-- Sidebar Footer: Log Out Button -->
        <div class="mt-auto p-4 border-t border-gray-700">
            <div class="flex items-center">
                <a href="{{ route('profile.edit') }}">
                    <div class="h-10 w-10 rounded-full bg-gray-600 flex items-center justify-center overflow-hidden">
                        @if(Auth::user()->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="h-10 w-10 object-cover rounded-full border">
                        @else
                            <span class="text-sm font-medium text-white">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                        @endif
                    </div>
                </a>
                <div :class="$store.sidebar.expanded ? 'ml-3' : 'hidden'" class="text-sm">
                    <p class="font-medium text-white">{{ Auth::user()->name }}</p>
                    <p class="text-gray-400">{{ ucfirst(Auth::user()->role) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit"
                    class="flex items-center px-3 py-1 rounded hover:bg-gray-700 transition text-xs text-red-500 hover:text-red-700 w-full justify-center"
                    title="{{ __('Log Out') }}">
                    <span :class="$store.sidebar.expanded ? 'ml-2 text-red-600 group-hover:text-red-700 font-medium' : 'hidden'">
                        {{ __('Log Out') }}
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>
