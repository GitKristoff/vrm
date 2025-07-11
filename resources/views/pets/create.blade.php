<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Register New Pet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('pets.store') }}" enctype="multipart/form-data">
                        @csrf

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
                                    type="text" name="name" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="md:col-span-1">
                                <x-input-label for="species" :value="__('Species')" />
                                    <select id="species" name="species" class="block mt-1 w-full rounded">
                                        <option value="Dog">Dog</option>
                                        <option value="Cat">Cat</option>
                                        <option value="Bird">Bird</option>
                                        <option value="Rabbit">Rabbit</option>
                                        <option value="Rodent">Rodent</option>
                                        <option value="Reptile">Reptile</option>
                                        <option value="Fish">Fish</option>
                                        <option value="Other">Other</option>
                                    </select>
                                <x-input-error :messages="$errors->get('species')" class="mt-2" />
                            </div>

                            <div class="md:col-span-1">
                                <x-input-label for="breed" :value="__('Breed')" />
                                <x-text-input id="breed" class="block mt-1 w-full"
                                    type="text" name="breed" />
                                <x-input-error :messages="$errors->get('breed')" class="mt-2" />
                            </div>

                            <div class="md:col-span-1">
                                <x-input-label for="age" :value="__('Age (years)')" />
                                <x-text-input id="age" class="block mt-1 w-full"
                                    type="number" name="age" min="0" max="50" required />
                                <x-input-error :messages="$errors->get('age')" class="mt-2" />
                            </div>

                            <div class="md:col-span-1">
                                <x-input-label for="weight" :value="__('Weight (kg)')" />
                                <x-text-input id="weight" class="block mt-1 w-full"
                                    type="number" step="0.1" name="weight" min="0" max="200" />
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
                                    placeholder="Previous illnesses, surgeries, chronic conditions"></textarea>
                                <x-input-error :messages="$errors->get('medical_history')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="allergies" :value="__('Allergies')" />
                                <textarea id="allergies" name="allergies" rows="2"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Known allergies or reactions"></textarea>
                                <x-input-error :messages="$errors->get('allergies')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button class="mr-3" onclick="window.history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Register Pet') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
