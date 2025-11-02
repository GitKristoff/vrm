<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Appointment Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Pet Information</h3>
                            <div class="mt-4 flex items-center">
                                @if($appointment->pet->profile_image)
                                    <div class="flex-shrink-0 h-16 w-16 rounded-full overflow-hidden mr-4">
                                        <img class="w-full h-full object-cover"
                                            src="{{ asset('storage/'.$appointment->pet->profile_image) }}"
                                            alt="{{ $appointment->pet->name }}">
                                    </div>
                                @endif
                                <div>
                                    <p class="text-lg font-semibold">{{ $appointment->pet->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $appointment->pet->species }} • {{ $appointment->pet->breed }}</p>
                                    <p class="text-sm text-gray-500">Age: {{ $appointment->pet->age }} years</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Appointment Details</h3>
                            <dl class="mt-4 space-y-2">
                                <div class="flex items-start">
                                    <dt class="w-32 flex-shrink-0 text-sm font-medium text-gray-500">Veterinarian</dt>
                                    <dd class="text-sm text-gray-900">{{ $appointment->veterinarian->user->name }}</dd>
                                </div>
                                <div class="flex items-start">
                                    <dt class="w-32 flex-shrink-0 text-sm font-medium text-gray-500">Date & Time</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $appointment->appointment_date->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}
                                        <span class="text-xs text-gray-500">(Asia/Manila)</span>
                                    </dd>
                                </div>
                                <div class="flex items-start">
                                    <dt class="w-32 flex-shrink-0 text-sm font-medium text-gray-500">Duration</dt>
                                    <dd class="text-sm text-gray-900">{{ $appointment->duration_minutes }} minutes</dd>
                                </div>
                                <div class="flex items-start">
                                    <dt class="w-32 flex-shrink-0 text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $appointment->status === 'scheduled' ? 'bg-blue-100 text-blue-800' :
                                               ($appointment->status === 'completed' ? 'bg-green-100 text-green-800' :
                                               ($appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="flex items-start">
                                    <dt class="w-32 flex-shrink-0 text-sm font-medium text-gray-500">Type</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ ucfirst($appointment->type) }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">Reason</h3>
                        <p class="mt-2 text-gray-700">{{ $appointment->reason }}</p>
                    </div>

                    @if($appointment->notes)
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900">Veterinarian Notes</h3>
                            <p class="mt-2 text-gray-700">{{ $appointment->notes }}</p>
                        </div>
                    @endif

                    <div class="mt-8 flex space-x-4">
                        @if($appointment->status === 'Scheduled')
                            @php
                                $isOverdue = $appointment->appointment_date < now();
                            @endphp

                            {{-- Approved: show Check-in to veterinarian/admin --}}
                            @if($appointment->approved)
                                @if(auth()->user()->role === 'veterinarian' || auth()->user()->role === 'admin')
                                    <a href="{{ route('appointments.checkin.create', $appointment) }}"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                        @if($isOverdue)
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
                                                });"
                                        @endif
                                    >
                                        Check-in
                                    </a>
                                @endif

                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 ml-2">
                                    Approved
                                </span>
                            @else
                                {{-- Not approved: show Approve button to assigned veterinarian or admin, otherwise show awaiting text --}}
                                @if((auth()->user()->role === 'veterinarian' && isset(auth()->user()->veterinarian) && $appointment->veterinarian_id === auth()->user()->veterinarian->id) || auth()->user()->role === 'admin')
                                    <form action="{{ route('appointments.approve', $appointment) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                            Approve
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 rounded ml-2">
                                        Awaiting approval
                                    </span>
                                @endif
                            @endif

                            {{-- Cancel button (kept behavior) --}}
                            @if(auth()->user()->role === 'veterinarian' && $appointment->veterinarian_id === auth()->user()->veterinarian->id)
                                <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                        onclick="confirmAction(event, 'Are you sure you want to cancel this appointment?')">
                                        Cancel Appointment
                                    </button>
                                </form>
                            @elseif(auth()->user()->role === 'owner')
                                <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                        onclick="confirmAction(event, 'Are you sure you want to cancel this appointment?')">
                                        Cancel Appointment
                                    </button>
                                </form>
                            @endif
                        @endif
                     </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
