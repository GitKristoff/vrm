<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $pet->name }}'s Profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Pet Info Section -->
                        <div class="md:w-1/3 mb-6 md:mb-0">
                            @if($pet->profile_image)
                                <div class="aspect-w-1 aspect-h-1 rounded-lg overflow-hidden shadow-md">
                                    <img src="{{ asset('storage/'.$pet->profile_image) }}"
                                        alt="{{ $pet->name }}"
                                        class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full aspect-w-1 aspect-h-1 flex items-center justify-center text-gray-500">
                                    No Image
                                </div>
                            @endif
                        </div>
                        <div class="md:w-2/3 md:pl-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Basic Info -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                                    <dl class="mt-2 space-y-2">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $pet->name }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Species</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $pet->species }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Breed</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $pet->breed ?? 'N/A' }}</dd>
                                        </div>
                                    </dl>
                                </div>
                                <!-- Health Details -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Health Details</h3>
                                    <dl class="mt-2 space-y-2">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Age</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $pet->age }} years</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Weight</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $pet->weight ? $pet->weight.' kg' : 'N/A' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Medical History</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $pet->medical_history ?? 'None recorded' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Allergies</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $pet->allergies ?? 'None recorded' }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                            <div class="mt-8 flex space-x-4">
                                <a href="{{ route('pets.edit', $pet) }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Edit Profile
                                </a>
                                <a href="{{ route('appointments.create', ['pet_id' => $pet->id]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Book Appointment
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Diagnosis Assistant Section -->
            <div class="mt-8 bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-8 bg-white border-b border-gray-200">
                    <h3 class="text-xl font-bold text-indigo-700 mb-6 flex items-center">
                        <i class="fas fa-robot mr-2"></i>AI Diagnosis Assistant
                    </h3>
                    <form id="ai-symptom-form" class="mb-6">
                        @csrf
                        <div class="mb-4">
                            <label for="symptoms" class="block text-sm font-medium text-gray-700">Describe your pet's symptoms</label>
                            <textarea id="symptoms" name="symptoms" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="E.g., vomiting, lethargy, loss of appetite..."></textarea>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-stethoscope mr-2"></i>Get Preliminary Diagnosis
                        </button>
                    </form>
                    <div id="ai-results" class="mt-4 p-4 bg-blue-50 rounded-lg" style="display: none;">
                        <span id="ai-loading" style="display:none;">Loading AI diagnosis...</span>
                        <h4 class="font-medium text-blue-800">AI Diagnosis Results</h4>
                        <p><strong>Condition:</strong> <span id="ai-diagnosis"></span></p>
                        <p><strong>Confidence:</strong> <span id="ai-confidence"></span></p>
                        <p><strong>Notes:</strong> <span id="ai-notes"></span></p>
                        <p><strong>Possible Conditions:</strong> <span id="ai-possible-conditions"></span></p>
                        <p><strong>Recommended Treatments:</strong> <span id="ai-recommended-treatments"></span></p>
                        <p><strong>Medication Interactions:</strong> <span id="ai-medication-interactions"></span></p>
                        <p><strong>Confidence Score:</strong> <span id="ai-confidence-score"></span></p>
                        <p><strong>AI Model Version:</strong> <span id="ai-model-version"></span></p>
                        <p><strong>Explanation:</strong> <span id="ai-explanation"></span></p>
                        <div class="mt-3 p-3 bg-blue-100 rounded">
                            <i class="fas fa-info-circle mr-2"></i> This is a preliminary diagnosis based on AI analysis. Please consult with a veterinarian for professional medical advice.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    document.getElementById('ai-symptom-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const symptoms = document.getElementById('symptoms').value;

        // Show loading indicator
        const aiResults = document.getElementById('ai-results');
        const aiLoading = document.getElementById('ai-loading');
        aiResults.style.display = 'block';
        aiLoading.style.display = 'inline';

        fetch("{{ route('ai.predict') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                symptoms: symptoms,
                pet_id: {{ $pet->id }}
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            aiLoading.style.display = 'none';
            document.getElementById('ai-diagnosis').textContent = data.diagnosis || data.possible_conditions?.[0] || 'Unknown';
            document.getElementById('ai-confidence').textContent =
                Math.round((data.confidence || data.confidence_score || 0) * 100) + '%';
            document.getElementById('ai-notes').textContent = data.additional_notes;
            document.getElementById('ai-possible-conditions').textContent = data.possible_conditions;
            document.getElementById('ai-recommended-treatments').textContent = data.recommended_treatments;
            document.getElementById('ai-medication-interactions').textContent = data.medication_interactions;
            document.getElementById('ai-confidence-score').textContent = data.confidence_score;
            document.getElementById('ai-model-version').textContent = data.ai_model_version || data.model_version;
            document.getElementById('ai-explanation').textContent = data.explanation;
            aiResults.style.display = 'block';
        })
        .catch(error => {
            aiLoading.style.display = 'none';
            aiResults.innerHTML = `
                <p class="text-red-600">Error: ${error.message}</p>
                <p class="text-sm">Please try again or contact support</p>
            `;
        });
    });
</script>
