<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pet: ') . $pet->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('pets.update', $pet) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Pet Information -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                                    Pet Information
                                </h3>
                            </div>

                            <div class="md:col-span-1">
                                <x-input-label for="name" :value="__('Pet Name')" />
                                <x-text-input id="name" class="block mt-1 w-full"
                                    type="text" name="name" value="{{ $pet->name }}" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="md:col-span-1">
                                <x-input-label for="species" :value="__('Species')" />
                                <select id="species" name="species" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Dog" {{ $pet->species === 'Dog' ? 'selected' : '' }}>Dog</option>
                                    <option value="Cat" {{ $pet->species === 'Cat' ? 'selected' : '' }}>Cat</option>
                                    <option value="Bird" {{ $pet->species === 'Bird' ? 'selected' : '' }}>Bird</option>
                                    <option value="Rabbit" {{ $pet->species === 'Rabbit' ? 'selected' : '' }}>Rabbit</option>
                                    <option value="Rodent" {{ $pet->species === 'Rodent' ? 'selected' : '' }}>Rodent</option>
                                    <option value="Reptile" {{ $pet->species === 'Reptile' ? 'selected' : '' }}>Reptile</option>
                                    <option value="Fish" {{ $pet->species === 'Fish' ? 'selected' : '' }}>Fish</option>
                                    <option value="Other" {{ $pet->species === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <x-input-error :messages="$errors->get('species')" class="mt-2" />
                            </div>

                            <div class="md:col-span-1">
                                <x-input-label for="breed" :value="__('Breed')" />
                                <x-text-input id="breed" class="block mt-1 w-full"
                                    type="text" name="breed" value="{{ $pet->breed }}" />
                                <x-input-error :messages="$errors->get('breed')" class="mt-2" />
                            </div>

                            <div class="md:col-span-1">
                                <x-input-label for="age" :value="__('Age (years)')" />
                                <x-text-input id="age" class="block mt-1 w-full"
                                    type="number" name="age" min="0" max="50" value="{{ $pet->age }}" required />
                                <x-input-error :messages="$errors->get('age')" class="mt-2" />
                            </div>

                            <div class="md:col-span-1">
                                <x-input-label for="weight" :value="__('Weight (kg)')" />
                                <x-text-input id="weight" class="block mt-1 w-full"
                                    type="number" step="0.1" name="weight" min="0" max="200" value="{{ $pet->weight }}" />
                                <x-input-error :messages="$errors->get('weight')" class="mt-2" />
                            </div>

                            <div class="md:col-span-1">
                                <x-input-label for="profile_image" :value="__('Profile Image')" />
                                <input type="file" class="block mt-1 w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100"
                                    id="profile_image" name="profile_image" />
                                <div class="text-sm text-gray-500 mt-1">Optional. Max 2MB.</div>
                                @error('profile_image')
                                    <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                                @enderror

                                @if($pet->profile_image)
                                    <div class="mt-2">
                                        <div class="w-24 h-24 rounded-lg overflow-hidden">
                                            <img src="{{ asset('storage/'.$pet->profile_image) }}"
                                                alt="Current profile image"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">Current image</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Medical Information -->
                            <div class="md:col-span-2 mt-6">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                                    Medical Information
                                </h3>
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="medical_history" :value="__('Medical History')" />
                                <textarea id="medical_history" name="medical_history" rows="3"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Previous illnesses, surgeries, chronic conditions">{{ $pet->medical_history }}</textarea>
                                <x-input-error :messages="$errors->get('medical_history')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="allergies" :value="__('Allergies')" />
                                <textarea id="allergies" name="allergies" rows="2"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Known allergies or reactions">{{ $pet->allergies }}</textarea>
                                <x-input-error :messages="$errors->get('allergies')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button class="mr-3" onclick="window.history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Update Pet') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
