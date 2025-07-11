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
                    <div class="flex flex-col md:flex-row">
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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        </div>
    </div>
</x-app-layout>
