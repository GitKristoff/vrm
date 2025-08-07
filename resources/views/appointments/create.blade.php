<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Appointment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('appointments.store') }}">
                        @csrf

                        <!-- Pet Selection -->
                        <div class="mb-6">
                            <x-input-label for="pet_id" :value="__('Select Pet')" />
                            <select id="pet_id" name="pet_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($pets as $pet)
                                    <option value="{{ $pet->id }}" {{ $pet->id == $selectedPet ? 'selected' : '' }}>
                                        {{ $pet->name }} ({{ $pet->species }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('pet_id')" class="mt-2" />
                        </div>

                        <!-- Veterinarian Selection -->
                        <div class="mb-2">
                            <x-input-label for="veterinarian_id" :value="__('Select Veterinarian')" />
                            <select id="veterinarian_id" name="veterinarian_id" onchange="showVetInfo()"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Veterinarian</option>
                                @foreach($vets as $vet)
                                    <option value="{{ $vet->id }}"
                                        data-working-days="{{ implode(',', $vet->working_days ?? []) }}"
                                        data-start-time="{{ $vet->start_time }}"
                                        data-end-time="{{ $vet->end_time }}"
                                        data-status="{{ $vet->status }}"
                                    >
                                        {{ $vet->user->name }} (Status: {{ ucfirst($vet->status) }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('veterinarian_id')" class="mt-2" />
                        </div>

                        <!-- Vet Info Displayed Here, outside the grid for clarity -->
                        <div id="vet-info" class="mb-6 text-sm text-gray-600 border rounded p-2 bg-gray-50"></div>

                        <!-- The rest of your form fields in a grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Appointment Date -->
                            <!-- Add timezone information to form -->
                        <input type="hidden" name="timezone" value="Asia/Manila">

                        <!-- Update datetime-local input -->
                        <input type="datetime-local" id="appointment_date" name="appointment_date"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            min="{{ now()->setTimezone('Asia/Manila')->format('Y-m-d\TH:i') }}">

                            <!-- Duration -->
                            @if(auth()->user()->role !== 'owner')
                                <!-- Duration field shown to vets/admins -->
                                <div>
                                    <x-input-label for="duration_minutes" :value="__('Duration (minutes)')" />
                                    <select id="duration_minutes" name="duration_minutes" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="15">15 minutes</option>
                                        <option value="30" selected>30 minutes</option>
                                        <option value="45">45 minutes</option>
                                        <option value="60">60 minutes</option>
                                    </select>
                                </div>
                            @else
                                <!-- Hidden default duration for owners -->
                                <input type="hidden" name="duration_minutes" value="30">
                            @endif

                            <!-- Reason -->
                            <div class="md:col-span-2">
                                <x-input-label for="reason" :value="__('Reason for Appointment')" />
                                <textarea id="reason" name="reason" rows="3"
                                          class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          placeholder="Describe the reason for this appointment"></textarea>
                                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                            </div>

                            <!-- Appointment Type -->
                            <div>
                                <x-input-label for="type" :value="__('Appointment Type')" />
                                <select name="type" id="type" required
                                        class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Type</option>
                                    <option value="vaccination">Vaccination</option>
                                    <option value="dental">Dental Care</option>
                                    <option value="checkup">Check-up</option>
                                    <option value="surgery">Surgery</option>
                                    <option value="other">Other</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <x-secondary-button class="mr-3" onclick="window.history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Create Appointment') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <script>
                    function showVetInfo() {
                        var select = document.getElementById('veterinarian_id');
                        var option = select.options[select.selectedIndex];
                        var days = option.getAttribute('data-working-days');
                        var start = option.getAttribute('data-start-time');
                        var end = option.getAttribute('data-end-time');
                        var status = option.getAttribute('data-status');
                        document.getElementById('vet-info').innerHTML =
                            (days ? 'Working Days: ' + days.replace(/,/g, ', ') + '<br>' : '') +
                            (start && end ? 'Hours: ' + start + ' - ' + end + '<br>' : '') +
                            (status ? 'Status: ' + status.charAt(0).toUpperCase() + status.slice(1) : '');
                    }
                    // Show info if a vet is pre-selected
                    document.addEventListener('DOMContentLoaded', function() {
                        showVetInfo();
                    });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
