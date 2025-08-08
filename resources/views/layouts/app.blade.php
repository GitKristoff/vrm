<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmAction(event, message) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.closest('form').submit();
                }
            });
        }
    </script>

    <!-- Day.js -->
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/plugin/relativeTime.js"></script>
    <script>
        dayjs.extend(window.dayjs_plugin_relativeTime);
    </script>

    <!-- FullCalendar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Dynamic Sidebar -->
        @auth
            <x-layout.sidebar />
        @endauth

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 min-h-screen transition-all duration-300"
            :class="{
                'lg:ml-64': $store.sidebar.expanded,
                'lg:ml-20': !$store.sidebar.expanded,
                'ml-0': window.innerWidth < 1024
            }"
            x-init="$nextTick(() => {
                // Close sidebar on mobile by default
                if (window.innerWidth < 1024) {
                    $store.sidebar.expanded = false;
                }
            })"
        >
            <!-- Page Heading -->
            @auth
                @if (isset($header))
                    <header class="bg-white shadow sticky top-0 z-10">
                        <div class="max-w-full py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                            <div class="flex items-center">
                                <!-- Mobile sidebar toggle button -->
                                <button @click="$store.sidebar.toggle()"
                                        class="mr-4 text-gray-500 hover:text-gray-700 lg:hidden">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                {{ $header }}
                            </div>
                            <div class="flex items-center space-x-4">
                            </div>
                        </div>
                    </header>
                @endif
            @endauth

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
