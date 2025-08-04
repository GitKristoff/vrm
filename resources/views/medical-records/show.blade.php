<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Medical History: {{ $pet->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Pet Information</h3>
                        <div class="mt-4 flex items-center">
                            @if($pet->profile_image)
                                <div class="flex-shrink-0 h-16 w-16 rounded-full overflow-hidden mr-4">
                                    <img class="w-full h-full object-cover"
                                        src="{{ asset('storage/'.$pet->profile_image) }}"
                                        alt="{{ $pet->name }}">
                                </div>
                            @endif
                            <div>
                                <p class="text-lg font-semibold">{{ $pet->name }}</p>
                                <p class="text-sm text-gray-600">{{ $pet->species }} • {{ $pet->breed }}</p>
                                <p class="text-sm text-gray-500">Age: {{ $pet->age }} years</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Medical Records</h3>

                        @if($records->count())
                            <div class="space-y-6">
                                @foreach($records as $record)
                                <div class="border border-gray-200 rounded-lg p-6">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($record->record_date)->format('M d, Y') }}
                                            </h4>
                                            <p class="text-sm text-gray-500">
                                                Recorded by Dr. {{ $record->veterinarian->user->name }}
                                            </p>
                                        </div>
                                        @if($record->appointment)
                                        <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                            Appointment: {{ \Carbon\Carbon::parse($record->appointment->appointment_date)->format('M d, Y') }}
                                        </div>
                                        @endif
                                    </div>

                                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-700 mb-2">Subjective Notes</h5>
                                            <p class="text-gray-600">{{ $record->subjective_notes }}</p>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-700 mb-2">Objective Findings</h5>
                                            <p class="text-gray-600">{{ $record->objective_notes }}</p>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-700 mb-2">Assessment</h5>
                                            <p class="text-gray-600">{{ $record->assessment }}</p>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-700 mb-2">Plan</h5>
                                            <p class="text-gray-600">{{ $record->plan }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-700">Temperature</h5>
                                            <p class="text-gray-600">{{ $record->temperature ? $record->temperature.'°C' : 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-700">Heart Rate</h5>
                                            <p class="text-gray-600">{{ $record->heart_rate ? $record->heart_rate.' bpm' : 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-700">Respiratory Rate</h5>
                                            <p class="text-gray-600">{{ $record->respiratory_rate ? $record->respiratory_rate.' bpm' : 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-700">Weight</h5>
                                            <p class="text-gray-600">{{ $record->weight ? $record->weight.' kg' : 'N/A' }}</p>
                                        </div>
                                    </div>

                                    @if($record->vaccination_history)
                                    <div class="mt-6">
                                        <h5 class="text-sm font-medium text-gray-700 mb-2">Vaccination History</h5>
                                        <p class="text-gray-600">{{ $record->vaccination_history }}</p>
                                    </div>
                                    @endif

                                    @if($record->medications->count())
                                    <div class="mt-6">
                                        <h5 class="text-sm font-medium text-gray-700 mb-2">Medications Prescribed</h5>
                                        <ul class="space-y-2">
                                            @foreach($record->medications as $medication)
                                            <li class="border-l-4 border-blue-500 pl-4 py-2">
                                                <div class="font-medium">{{ $medication->name }}</div>
                                                <div class="text-sm text-gray-600">Dosage: {{ $medication->dosage }}</div>
                                                <div class="text-sm text-gray-600">Frequency: {{ $medication->frequency }}</div>
                                                <div class="text-sm text-gray-600">
                                                    Duration: {{ \Carbon\Carbon::parse($medication->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($medication->end_date)->format('M d, Y') }}
                                                </div>
                                                <div class="text-sm text-gray-600">Purpose: {{ $medication->purpose }}</div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                {{ $records->links() }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No medical records</h3>
                                <p class="mt-1 text-sm text-gray-500">No medical records found for this pet.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Appointment Type Records --}}
                    <div class="mb-4 p-4 bg-gray-50 rounded border">
                        <h4 class="font-semibold mb-2">Appointment Type Records</h4>
                        <ul>
                            @foreach(['vaccination' => 'Vaccination', 'dental' => 'Dental Care', 'checkup' => 'Check-up', 'surgery' => 'Surgery', 'other' => 'Other'] as $key => $label)
                                <li>
                                    <strong>{{ $label }}:</strong>
                                    {{ $typeCounts[$key] > 0 ? $typeCounts[$key] . ' record(s)' : 'None recorded' }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    {{-- End Appointment Type Records --}}

                    <a href="{{ route('medical-records.download', $pet->id) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
