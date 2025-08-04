<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $veterinarian->user->name }}'s Profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center mb-8">
                        <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-3xl font-bold text-gray-600 overflow-hidden border-4 border-white shadow">
                            @if($veterinarian->user->profile_picture)
                                <img src="{{ asset('storage/' . $veterinarian->user->profile_picture) }}"
                                     alt="Profile Picture"
                                     class="w-24 h-24 object-cover rounded-full" />
                            @else
                                {{ strtoupper(substr($veterinarian->user->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="ml-6">
                            <h1 class="text-2xl font-bold">{{ $veterinarian->user->name }}</h1>
                            <p class="text-gray-600">{{ $veterinarian->user->email }}</p>
                            @if($veterinarian->is_admin)
                                <span class="mt-2 inline-block px-3 py-1 text-sm font-semibold text-green-800 bg-green-100 rounded-full">
                                    Administrator
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Professional Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">License Number</p>
                                    <p class="font-medium">{{ $veterinarian->license_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Specialization</p>
                                    <p class="font-medium">{{ $veterinarian->specialization }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Phone</p>
                                    <p class="font-medium">{{ $veterinarian->phone }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Registered</p>
                                    <p class="font-medium">{{ $veterinarian->user->created_at->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Last Updated</p>
                                    <p class="font-medium">{{ $veterinarian->updated_at->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <p class="font-medium text-green-600">Active</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <a href="{{ route('admin.veterinarians.index') }}"
                           class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 mr-3">
                            Back to List
                        </a>
                        <a href="{{ route('admin.veterinarians.edit', $veterinarian) }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
