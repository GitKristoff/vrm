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
            'w-20': !$store.sidebar.expanded
        }"
        class="bg-gray-800 text-white h-screen transition-all duration-300 transform flex flex-col"
    >
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-700">
            <div class="flex items-center" x-show="$store.sidebar.expanded">
                <x-application-logo class="block h-9 w-auto text-white"/>
                <span class="ml-3 text-xl font-semibold">VRMS</span>
            </div>
            <button @click="$store.sidebar.toggle()"
                    class="text-gray-400 hover:text-white focus:outline-none">
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
                <!-- Icon (using Heroicons) -->
                <svg class="flex-shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <!-- Dynamic icons would be implemented here -->
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span :class="$store.sidebar.expanded ? 'ml-3' : 'hidden'" class="transition-all duration-200">
                    {{ $item['name'] }}
                </span>
            </a>
            @endforeach
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-gray-700">
            <div class="flex items-center justify-center">
                <div class="h-10 w-10 rounded-full bg-gray-600 flex items-center justify-center">
                    <a href="{{route('profile.edit')}}">
                        <span class="text-sm font-medium">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                    </a>
                </div>
                <div :class="$store.sidebar.expanded ? 'ml-3' : 'hidden'" class="text-sm">
                    <p class="font-medium text-white">{{ Auth::user()->name }}</p>
                    <p class="text-gray-400">{{ ucfirst(Auth::user()->role) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
