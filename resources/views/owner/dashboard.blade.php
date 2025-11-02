<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
            {{ __('My Pets') }}
        </h2>
    </x-slot>

    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 bg-white border-b border-gray-200">

                    {{-- NEW: Remaining slots for today --}}
                    @php
                        $limits = config('appointment_limits', []);
                        $todayManila = \Carbon\Carbon::now('Asia/Manila');
                        $dayStartUtc = $todayManila->copy()->startOfDay()->setTimezone('UTC');
                        $dayEndUtc = $todayManila->copy()->endOfDay()->setTimezone('UTC');

                        $remaining = [];
                        foreach ($limits as $type => $limit) {
                            $count = \App\Models\Appointment::where('type', $type)
                                ->where('status', 'Scheduled')
                                ->whereBetween('appointment_date', [$dayStartUtc, $dayEndUtc])
                                ->count();
                            $remaining[$type] = max(0, $limit - $count);
                        }
                    @endphp

                    <div class="mb-6 grid grid-cols-1 sm:grid-cols-4 gap-3 opacity-70">
                        <div class="p-3 bg-white border rounded text-center">
                            <div class="text-xs text-gray-500">Vaccination left</div>
                            <div class="text-lg font-semibold">{{ $remaining['vaccination'] ?? '-' }}</div>
                        </div>
                        <div class="p-3 bg-white border rounded text-center">
                            <div class="text-xs text-gray-500">Dental left</div>
                            <div class="text-lg font-semibold">{{ $remaining['dental'] ?? '-' }}</div>
                        </div>
                        <div class="p-3 bg-white border rounded text-center">
                            <div class="text-xs text-gray-500">Check-up left</div>
                            <div class="text-lg font-semibold">{{ $remaining['checkup'] ?? '-' }}</div>
                        </div>
                        <div class="p-3 bg-white border rounded text-center">
                            <div class="text-xs text-gray-500">Surgery left</div>
                            <div class="text-lg font-semibold">{{ $remaining['surgery'] ?? '-' }}</div>
                        </div>
                    </div>
                    {{-- /NEW --}}

                    <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-3">
                        <h3 class="text-base sm:text-lg font-medium">Your Pets</h3>
                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <a href="{{ route('appointments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-center w-full sm:w-auto">
                                New Appointment
                            </a>
                            <a href="{{ route('pets.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-center w-full sm:w-auto">
                                Add New Pet
                            </a>
                        </div>
                    </div>

                    @if($pets->count())
                        <!-- Mobile Card List -->
                        <div class="sm:hidden space-y-4">
                            @foreach($pets as $pet)
                                <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-between shadow">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 rounded-full overflow-hidden flex-shrink-0">
                                            @if($pet->profile_image)
                                                <img class="w-full h-full object-cover"
                                                    src="{{ asset('storage/'.$pet->profile_image) }}"
                                                    alt="{{ $pet->name }}">
                                            @else
                                                <div class="bg-gray-200 border-2 border-dashed w-full h-full"></div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="font-semibold text-gray-900">{{ $pet->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $pet->species }} &middot; {{ $pet->breed }}</div>
                                            <div class="text-xs text-gray-500">{{ $pet->age }} years</div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2 ml-2">
                                        <a href="{{ route('pets.show', $pet) }}" class="text-blue-600 hover:text-blue-900 text-xs font-medium">Details</a>
                                        <a href="{{ route('appointments.create', ['pet_id' => $pet->id]) }}" class="text-green-600 hover:text-green-900 text-xs font-medium">Book</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Desktop Table -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Pet</th>
                                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Species & Breed</th>
                                        <th scope="col" class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                        <th scope="col" class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pets as $pet)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                                    @if($pet->profile_image)
                                                        <img class="w-full h-full object-cover"
                                                            src="{{ asset('storage/'.$pet->profile_image) }}"
                                                            alt="{{ $pet->name }}">
                                                    @else
                                                        <div class="bg-gray-200 border-2 border-dashed w-full h-full"></div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="font-medium text-gray-900">{{ $pet->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-gray-900">{{ $pet->species }}</div>
                                            <div class="text-gray-500">{{ $pet->breed }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-gray-900">{{ $pet->age }} years</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                            <div class="flex gap-2 justify-end">
                                                <a href="{{ route('pets.show', $pet) }}" class="text-blue-600 hover:text-blue-900 font-medium">Details</a>
                                                <a href="{{ route('appointments.create', ['pet_id' => $pet->id]) }}" class="text-green-600 hover:text-green-900 font-medium">Book</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                            </svg>
                            <h3 class="mt-2 font-medium text-gray-900">No pets</h3>
                            <p class="mt-1 text-gray-500">You haven't registered any pets yet.</p>
                            <div class="mt-6">
                                <a href="{{ route('pets.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add New Pet
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
