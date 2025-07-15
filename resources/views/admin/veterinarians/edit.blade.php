<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Veterinarian: {{ $veterinarian->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.veterinarians.update', $veterinarian) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- License Number -->
                            <div>
                                <x-input-label for="license_number" :value="__('License Number')" />
                                <x-text-input id="license_number" class="block mt-1 w-full" type="text"
                                    name="license_number" :value="old('license_number', $veterinarian->license_number)" required autofocus />
                                <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                            </div>

                            <!-- Specialization -->
                            <div>
                                <x-input-label for="specialization" :value="__('Specialization')" />
                                <x-text-input id="specialization" class="block mt-1 w-full" type="text"
                                    name="specialization" :value="old('specialization', $veterinarian->specialization)" required />
                                <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
                            </div>

                            <!-- Phone -->
                            <div class="md:col-span-2">
                                <x-input-label for="phone" :value="__('Phone')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text"
                                    name="phone" :value="old('phone', $veterinarian->phone)" required />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            {{-- Admin Privileges --}}
                            <div class="md:col-span-2 mt-4">
                                <div class="flex items-center">
                                    <input id="is_admin" name="is_admin" type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        {{ $veterinarian->is_admin ? 'checked' : '' }}>
                                    <label for="is_admin" class="ml-2 block text-sm text-gray-700">
                                        Grant Administrator Privileges
                                    </label>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    When checked, this veterinarian will have access to admin features
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <a href="{{ route('admin.veterinarians.show', $veterinarian) }}"
                                class="text-gray-600 hover:text-gray-900 mr-4">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Veterinarian') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
