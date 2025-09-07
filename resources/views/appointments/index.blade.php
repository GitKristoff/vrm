<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Appointments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-2">
                        <h3 class="text-lg font-medium">Manage Appointments</h3>
                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <a href="{{ route('appointments.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 w-full sm:w-auto">
                                Create New Appointment
                            </a>
                            <a href="{{ route('appointments.calendar') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 w-full sm:w-auto">
                                View Calendar
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($appointments->count())
                        <!-- Mobile Cards -->
                        <div class="sm:hidden space-y-4">
                            @foreach($appointments as $appointment)
                            <div class="bg-white rounded-lg shadow p-4 flex flex-col gap-3 border border-gray-100">
                                <div class="flex items-center gap-3">
                                    @if($appointment->pet->profile_image)
                                        <img class="h-12 w-12 rounded-full object-cover border" src="{{ asset('storage/'.$appointment->pet->profile_image) }}" alt="{{ $appointment->pet->name }}">
                                    @else
                                        <div class="rounded-full bg-gray-200 border-2 border-dashed w-12 h-12"></div>
                                    @endif
                                    <div>
                                        <div class="text-base font-semibold text-gray-900">{{ $appointment->pet->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $appointment->pet->species }}</div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <div>
                                        <span class="font-semibold">Veterinarian:</span>
                                        <span class="text-gray-700">{{ $appointment->veterinarian->user->name ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold">Date & Time:</span>
                                        <span class="text-gray-700">{{ $appointment->appointment_date->format('M d, Y') }} {{ $appointment->appointment_date->format('h:i A') }}</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold">Reason:</span>
                                        <span class="text-gray-700">{{ Str::limit($appointment->reason, 30) }}</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold">Status:</span>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $appointment->status === 'scheduled' ? 'bg-blue-100 text-blue-800' :
                                               ($appointment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-2 mt-2">
                                    <a href="{{ route('appointments.show', $appointment) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">View</a>
                                    @if($appointment->status === 'Scheduled')
                                        @if(auth()->user()->role === 'veterinarian' && $appointment->veterinarian_id === auth()->user()->veterinarian->id)
                                            <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium"
                                                    onclick="confirmAction(event, 'Are you sure you want to cancel this appointment?')">
                                                    Cancel
                                                </button>
                                            </form>
                                        @elseif(auth()->user()->role === 'owner')
                                            <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium"
                                                    onclick="confirmAction(event, 'Are you sure you want to cancel this appointment?')">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <!-- Desktop Table -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Veterinarian</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($appointments as $appointment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap flex flex-row items-center">
                                            <div class="flex items-center">
                                                @if($appointment->pet->profile_image)
                                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                                        <img class="w-full h-full object-cover"
                                                            src="{{ asset('storage/'.$appointment->pet->profile_image) }}"
                                                            alt="{{ $appointment->pet->name }}">
                                                    </div>
                                                @else
                                                    <div class="rounded-full bg-gray-200 border-2 border-dashed w-10 h-10"></div>
                                                @endif
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->pet->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $appointment->pet->species }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $appointment->veterinarian->user->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $appointment->appointment_date->format('M d, Y') }}</div>
                                            <div class="text-sm text-gray-500">{{ $appointment->appointment_date->format('h:i A') }}</div>
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
                                            <div class="flex gap-2">
                                                <a href="{{ route('appointments.show', $appointment) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                                @if($appointment->status === 'Scheduled')
                                                    @if(auth()->user()->role === 'veterinarian' && $appointment->veterinarian_id === auth()->user()->veterinarian->id)
                                                        <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                                onclick="confirmAction(event, 'Are you sure you want to cancel this appointment?')">
                                                                Cancel
                                                            </button>
                                                        </form>
                                                    @elseif(auth()->user()->role === 'owner')
                                                        <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                                onclick="confirmAction(event, 'Are you sure you want to cancel this appointment?')">
                                                                Cancel
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
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
                                        No appointments scheduled yet.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
