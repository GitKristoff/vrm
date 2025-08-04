<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Complete Appointment: {{ $appointment->pet->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('appointments.checkin.store', $appointment) }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- SOAP Sections -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">SOAP Notes</h3>
                            </div>

                            <div>
                                <x-input-label for="subjective_notes" :value="__('Subjective Notes')" />
                                <textarea id="subjective_notes" name="subjective_notes" rows="4" required
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Owner's concerns, pet's behavior, symptoms, etc.">{{ old('subjective_notes') }}</textarea>
                            </div>

                            <div class="mt-4 mb-4">
                                <button type="button" id="ai-suggestion-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-robot mr-2"></i>Get AI Suggestion
                                </button>
                                <div id="ai-results" class="mt-3 p-3 bg-blue-50 rounded-lg" style="display:none;">
                                    <h5 class="font-medium text-blue-800">AI Diagnosis Suggestion</h5>
                                    <p><strong>Condition:</strong> <span id="ai-diagnosis"></span></p>
                                    <p><strong>Confidence:</strong> <span id="ai-confidence"></span></p>
                                    <p><strong>Notes:</strong> <span id="ai-notes"></span></p>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="objective_notes" :value="__('Objective Findings')" />
                                <textarea id="objective_notes" name="objective_notes" rows="4" required
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Physical exam findings, test results, etc.">{{ old('objective_notes') }}</textarea>
                            </div>

                            <div>
                                <x-input-label for="assessment" :value="__('Assessment')" />
                                <textarea id="assessment" name="assessment" rows="4" required
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Diagnosis, differentials, problem list">{{ old('assessment') }}</textarea>
                            </div>

                            <div>
                                <x-input-label for="plan" :value="__('Plan')" />
                                <textarea id="plan" name="plan" rows="4" required
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Treatment plan, further tests, follow-up">{{ old('plan') }}</textarea>
                            </div>

                            <!-- Vital Signs -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Vital Signs</h3>
                            </div>

                            <div>
                                <x-input-label for="temperature" :value="__('Temperature (°C)')" />
                                <x-text-input id="temperature" name="temperature" type="number" step="0.1"
                                    class="block mt-1 w-full" value="{{ old('temperature') }}"
                                    placeholder="37.5" />
                            </div>

                            <div>
                                <x-input-label for="heart_rate" :value="__('Heart Rate (bpm)')" />
                                <x-text-input id="heart_rate" name="heart_rate" type="number"
                                    class="block mt-1 w-full" value="{{ old('heart_rate') }}"
                                    placeholder="120" />
                            </div>

                            <div>
                                <x-input-label for="respiratory_rate" :value="__('Respiratory Rate (bpm)')" />
                                <x-text-input id="respiratory_rate" name="respiratory_rate" type="number"
                                    class="block mt-1 w-full" value="{{ old('respiratory_rate') }}"
                                    placeholder="30" />
                            </div>

                            <div>
                                <x-input-label for="weight" :value="__('Weight (kg)')" />
                                <x-text-input id="weight" name="weight" type="number" step="0.1"
                                    class="block mt-1 w-full" value="{{ old('weight') }}"
                                    placeholder="5.2" />
                            </div>

                            <!-- Vaccination History -->
                            <div class="md:col-span-2">
                                <x-input-label for="vaccination_history" :value="__('Vaccination History')" />
                                <textarea id="vaccination_history" name="vaccination_history" rows="3"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Vaccinations administered today">{{ old('vaccination_history') }}</textarea>
                            </div>

                           <!-- Medications -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Medications Prescribed</h3>
                                <div id="medications-container">
                                    <div class="medication-form grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                                        <div class="md:col-span-2">
                                            <x-input-label for="medications[0][name]" :value="__('Medication Name')" />
                                            <x-text-input id="medications[0][name]" name="medications[0][name]" type="text" required class="w-full" />
                                        </div>
                                        <div>
                                            <x-input-label for="medications[0][dosage]" :value="__('Dosage')" />
                                            <x-text-input id="medications[0][dosage]" name="medications[0][dosage]" type="text" required class="w-full" />
                                        </div>
                                        <div>
                                            <x-input-label for="medications[0][frequency]" :value="__('Frequency')" />
                                            <x-text-input id="medications[0][frequency]" name="medications[0][frequency]" type="text" required class="w-full" />
                                        </div>

                                        <div class="md:col-span-2">
                                            <x-input-label for="medications[0][purpose]" :value="__('Purpose')" />
                                            <x-text-input id="medications[0][purpose]" name="medications[0][purpose]" type="text" required class="w-full" />
                                        </div>
                                        <div>
                                            <x-input-label for="medications[0][start_date]" :value="__('Start Date')" />
                                            <x-text-input id="medications[0][start_date]" name="medications[0][start_date]" type="date" required class="w-full" />
                                        </div>
                                        <div>
                                            <x-input-label for="medications[0][end_date]" :value="__('End Date')" />
                                            <x-text-input id="medications[0][end_date]" name="medications[0][end_date]" type="date" required class="w-full" />
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="add-medication"
                                    class="mt-2 inline-flex items-center px-3 py-1 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                                    <i class="fas fa-plus mr-1"></i> Add Medication
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <x-secondary-button class="mr-3" onclick="window.history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Complete Appointment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let medicationIndex = 1;
            const container = document.getElementById('medications-container');

            document.getElementById('add-medication').addEventListener('click', function() {
                const newForm = document.createElement('div');
                newForm.className = 'medication-form grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 bg-gray-50 rounded-lg';
                newForm.innerHTML = `
                    <div class="md:col-span-2">
                        <x-input-label for="medications[${medicationIndex}][name]" :value="__('Medication Name')" />
                        <x-text-input id="medications[${medicationIndex}][name]" name="medications[${medicationIndex}][name]" type="text" required class="w-full" />
                    </div>
                    <div>
                        <x-input-label for="medications[${medicationIndex}][dosage]" :value="__('Dosage')" />
                        <x-text-input id="medications[${medicationIndex}][dosage]" name="medications[${medicationIndex}][dosage]" type="text" required class="w-full" />
                    </div>
                    <div>
                        <x-input-label for="medications[${medicationIndex}][frequency]" :value="__('Frequency')" />
                        <x-text-input id="medications[${medicationIndex}][frequency]" name="medications[${medicationIndex}][frequency]" type="text" required class="w-full" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="medications[${medicationIndex}][purpose]" :value="__('Purpose')" />
                        <x-text-input id="medications[${medicationIndex}][purpose]" name="medications[${medicationIndex}][purpose]" type="text" required class="w-full" />
                    </div>
                    <div>
                        <x-input-label for="medications[${medicationIndex}][start_date]" :value="__('Start Date')" />
                        <x-text-input id="medications[${medicationIndex}][start_date]" name="medications[${medicationIndex}][start_date]" type="date" required class="w-full" />
                    </div>
                    <div>
                        <x-input-label for="medications[${medicationIndex}][end_date]" :value="__('End Date')" />
                        <x-text-input id="medications[${medicationIndex}][end_date]" name="medications[${medicationIndex}][end_date]" type="date" required class="w-full" />
                    </div>
                    <div class="md:col-span-4 flex justify-end">
                        <button type="button" class="remove-medication text-red-600 hover:text-red-800">
                            <i class="fas fa-trash mr-1"></i> Remove Medication
                        </button>
                    </div>
                `;

                container.appendChild(newForm);
                medicationIndex++;

                // Add event listener to remove button
                newForm.querySelector('.remove-medication').addEventListener('click', function() {
                    container.removeChild(newForm);
                });
            });
        });

        document.getElementById('ai-suggestion-btn').addEventListener('click', function() {
            const symptoms = document.getElementById('subjective_notes').value;
            const petId = {{ $appointment->pet_id }};

            // Show loading indicator
            const aiResults = document.getElementById('ai-results');
            aiResults.style.display = 'block';
            aiResults.innerHTML = '<p>Loading AI suggestion...</p>';

            fetch("{{ route('ai.predict') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    symptoms: symptoms,
                    pet_id: petId
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('ai-diagnosis').textContent = data.diagnosis || data.possible_conditions?.[0] || 'Unknown';
                document.getElementById('ai-confidence').textContent =
                    Math.round((data.confidence || data.confidence_score || 0) * 100) + '%';
                document.getElementById('ai-notes').textContent = data.additional_notes;
                aiResults.style.display = 'block';

                // Auto-fill assessment field with AI suggestion
                document.getElementById('assessment').value =
                    `AI Suggestion: ${data.diagnosis}\n${data.additional_notes}`;
            })
            .catch(error => {
                console.error('AI Error:', error);
                aiResults.innerHTML = `
                    <p class="text-red-600">Error: ${error.message}</p>
                    <p class="text-sm">Please try again or contact support</p>
                `;
            });
        });
    </script>
</x-app-layout>
