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
                                        {{ $appointment->appointment_date->format('M d, Y h:i A') }}
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
                                            {{ $appointment->status === 'Scheduled' ? 'bg-blue-100 text-blue-800' :
                                               ($appointment->status === 'Completed' ? 'bg-green-100 text-green-800' :
                                               ($appointment->status === 'Cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
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
                            <form action="{{ route('appointments.destroy', $appointment) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    onclick="confirmAction(event, 'Are you sure you want to cancel this appointment?')">
                                    Cancel Appointment
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
