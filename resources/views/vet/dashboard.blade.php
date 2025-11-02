<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Veterinarian Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Admin Stats (if applicable) -->
            @if($isAdmin ?? false)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium mb-2">Total Users</h3>
                        <p class="text-3xl font-bold">{{ $adminStats['totalUsers'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium mb-2">Total Veterinarians</h3>
                        <p class="text-3xl font-bold">{{ $adminStats['totalVets'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium mb-2">Total Appointments</h3>
                        <p class="text-3xl font-bold">{{ $adminStats['totalAppointments'] }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-medium mb-4">Admin Actions</h3>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.veterinarians.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Add Veterinarian
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                            Manage Users
                        </a>
                    </div>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Today's Appointments</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $todaysAppointments }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full mr-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Completed</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $completedAppointments }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full mr-4">
                            <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Scheduled</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $scheduledAppointments }}</p>
                        </div>
                    </div>
                </div>

                {{-- Approved appointments card --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-emerald-100 p-3 rounded-full mr-4">
                            <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Approved</p>
                            @php
                                $approvedCount = $appointments
                                    ? $appointments->filter(function($a) {
                                        $status = strtolower($a->status ?? '');
                                        // include only approved and not completed
                                        return $status !== 'completed' && ($status === 'approved' || (!empty($a->approved) && $a->approved));
                                    })->count()
                                    : 0;
                            @endphp
                            <p class="text-2xl font-semibold text-gray-900">{{ $approvedCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add remaining slots overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                @php $rl = $remainingLimits ?? []; @endphp
                <div class="bg-white p-4 rounded-lg border text-center">
                    <p class="text-sm text-gray-600">Vaccination left</p>
                    <p class="text-2xl font-semibold">{{ $rl['vaccination'] ?? '-' }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg border text-center">
                    <p class="text-sm text-gray-600">Dental left</p>
                    <p class="text-2xl font-semibold">{{ $rl['dental'] ?? '-' }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg border text-center">
                    <p class="text-sm text-gray-600">Check-up left</p>
                    <p class="text-2xl font-semibold">{{ $rl['checkup'] ?? '-' }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg border text-center">
                    <p class="text-sm text-gray-600">Surgery left</p>
                    <p class="text-2xl font-semibold">{{ $rl['surgery'] ?? '-' }}</p>
                </div>
            </div>

            <!-- Approved Appointments List (quick access) -->
            @php
                $approvedAppointments = $appointments
                    ? $appointments->filter(function($a) {
                        $status = strtolower($a->status ?? '');
                        return $status !== 'completed' && ($status === 'approved' || (!empty($a->approved) && $a->approved));
                    })->sortBy('appointment_date')
                    : collect();
            @endphp

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Approved Appointments</h3>
                    <a href="{{ route('appointments.index') }}" class="text-sm text-indigo-600 hover:underline">See all</a>
                </div>

                @if($approvedAppointments->count())
                    <ul class="divide-y divide-gray-100">
                        @foreach($approvedAppointments->take(5) as $appointment)
                            <li class="py-3 flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->pet->name ?? 'Unknown Pet' }}</div>
                                    <div class="text-sm text-gray-500">{{ $appointment->appointment_date->format('M d, Y h:i A') }}</div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('appointments.show', $appointment) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">View</a>

                                    @if((auth()->user()->role === 'veterinarian' && isset(auth()->user()->veterinarian) && $appointment->veterinarian_id === auth()->user()->veterinarian->id) || auth()->user()->role === 'admin')
                                        <a href="{{ route('appointments.checkin.create', $appointment) }}" class="text-green-600 hover:text-green-900 text-sm ml-2">
                                            Check-in
                                        </a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-sm text-gray-600">No approved appointments at the moment.</div>
                @endif
            </div>

            <!-- Upcoming Appointments Table -->
            @if(!($isAdmin ?? false))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Upcoming Appointments</h3>

                        <a href="{{ route('appointments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            New Appointment
                        </a>
                    </div>

                    @php
                        // Filter appointments for the table:
                        // - Only scheduled appointments
                        // - Starting from today
                        $upcomingAppointments = $appointments
                            ? $appointments->filter(function($appointment) {
                                return strtolower($appointment->status ?? '') === 'scheduled';
                            })->sortBy('appointment_date')
                            : collect();
                    @endphp

                    @if($upcomingAppointments->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($upcomingAppointments as $appointment)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $appointment->pet->name ?? 'Unknown Pet' }}</div>
                                                        <div class="text-sm text-gray-500">{{ $appointment->pet->species ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $appointment->appointment_date->format('M d, Y') }}</div>
                                                <div class="text-sm text-gray-500">{{ $appointment->appointment_date->format('h:i A') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $appointment->pet->owner->user->name ?? 'Unknown Owner' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ Str::limit($appointment->reason, 30) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $appointment->status === 'scheduled' ? 'bg-blue-100 text-blue-800' :
                                                       ($appointment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('appointments.show', $appointment) }}"

                                                    class="text-indigo-600 hover:text-indigo-900">View</a>

                                                @if($appointment->status === 'scheduled')
                                                    @php
                                                        $isOverdue = $appointment->appointment_date < now();
                                                    @endphp

                                                    @if($appointment->approved)
                                                        {{-- Approved: show check-in (with same overdue confirm behavior) --}}
                                                        @if($isOverdue)
                                                            <a href="{{ route('appointments.checkin.create', $appointment) }}"
                                                            class="text-green-600 hover:text-green-900 ml-3"
                                                            onclick="event.preventDefault();
                                                                        Swal.fire({
                                                                            title: 'Appointment Overdue',
                                                                            text: 'This appointment is overdue (scheduled for {{ $appointment->appointment_date->format('M d, Y h:i A') }}). Are you sure you want to check in?',
                                                                            icon: 'warning',
                                                                            showCancelButton: true,
                                                                            confirmButtonColor: '#3085d6',
                                                                            cancelButtonColor: '#d33',
                                                                            confirmButtonText: 'Yes, check in'
                                                                        }).then((result) => {
                                                                            if (result.isConfirmed) {
                                                                                window.location.href = '{{ route('appointments.checkin.create', $appointment) }}';
                                                                            }
                                                                        });">
                                                                Check-in
                                                            </a>
                                                        @else
                                                            <a href="{{ route('appointments.checkin.create', $appointment) }}"
                                                            class="text-green-600 hover:text-green-900 ml-3">
                                                                Check-in
                                                            </a>
                                                        @endif
                                                    @else
                                                        {{-- Not approved: show Approve button for assigned veterinarian --}}
                                                        @if(auth()->user()->role === 'veterinarian' && auth()->user()->veterinarian && auth()->user()->veterinarian->id === $appointment->veterinarian_id)
                                                            <form method="POST" action="{{ route('appointments.approve', $appointment) }}" class="inline-block ml-3">
                                                                @csrf
                                                                <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                                            </form>
                                                        @else
                                                            <span class="ml-3 text-sm text-gray-500">Awaiting approval</span>
                                                        @endif
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        No upcoming appointments scheduled. Enjoy your free time!
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- All Scheduled Appointments Table (Admin View) -->
            @if($isAdmin ?? false)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">All Scheduled Appointments</h3>

                    @php
                        // Show all scheduled appointments, including overdue
                        $scheduledAppointmentsList = $appointments
                            ? $appointments->filter(function($appointment) {
                                return strtolower($appointment->status ?? '') === 'scheduled';
                            })->sortBy('appointment_date')
                            : collect();
                    @endphp

                    @if($scheduledAppointmentsList->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($scheduledAppointmentsList as $appointment)
                                    <tr class="hover:bg-gray-50 {{ $appointment->appointment_date < now() ? 'bg-red-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->pet->name ?? 'Unknown Pet' }}</div>
                                                    <div class="text-sm text-gray-500">{{ $appointment->pet->species ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $appointment->appointment_date->format('M d, Y') }}</div>
                                            <div class="text-sm text-gray-500">{{ $appointment->appointment_date->format('h:i A') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $appointment->pet->owner->user->name ?? 'Unknown Owner' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ Str::limit($appointment->reason, 30) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $appointment->status === 'scheduled' ? 'bg-blue-100 text-blue-800' :
                                                   ($appointment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('appointments.show', $appointment) }}"
                                                class="text-indigo-600 hover:text-indigo-900">View</a>

                                            @if($appointment->status === 'Scheduled')
                                                @php
                                                    $isOverdue = $appointment->appointment_date < now();
                                                @endphp

                                                @if($isOverdue)
                                                    <a href="{{ route('appointments.checkin.create', $appointment) }}"
                                                    class="text-green-600 hover:text-green-900 ml-3"
                                                    onclick="event.preventDefault();
                                                                Swal.fire({
                                                                    title: 'Appointment Overdue',
                                                                    text: 'This appointment is overdue (scheduled for {{ $appointment->appointment_date->format('M d, Y h:i A') }}). Are you sure you want to check in?',
                                                                    icon: 'warning',
                                                                    showCancelButton: true,
                                                                    confirmButtonColor: '#3085d6',
                                                                    cancelButtonColor: '#d33',
                                                                    confirmButtonText: 'Yes, check in'
                                                                }).then((result) => {
                                                                    if (result.isConfirmed) {
                                                                        window.location.href = '{{ route('appointments.checkin.create', $appointment) }}';
                                                                    }
                                                                });">
                                                        Check-in
                                                    </a>
                                                @else
                                                    <a href="{{ route('appointments.checkin.create', $appointment) }}"
                                                    class="text-green-600 hover:text-green-900 ml-3">
                                                        Check-in
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        No scheduled appointments found.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
