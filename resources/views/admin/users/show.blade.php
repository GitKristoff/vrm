<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->name }}'s Profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center mb-8">
                        <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-3xl font-bold text-gray-600 overflow-hidden border-4 border-white shadow">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                     alt="Profile Picture"
                                     class="w-24 h-24 object-cover rounded-full" />
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="ml-6">
                            <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            <span class="mt-2 inline-block px-3 py-1 text-sm font-semibold rounded-full
                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' :
                                   ($user->role === 'veterinarian' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Registered</p>
                                    <p class="font-medium">{{ $user->created_at->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Last Updated</p>
                                    <p class="font-medium">{{ $user->updated_at->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <p class="font-medium text-green-600">Active</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <a href="{{ route('admin.users.index') }}"
                           class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 mr-3">
                            Back to List
                        </a>
                        @if($user->role === 'veterinarian')
                            <a href="{{ route('admin.veterinarians.edit', $user->veterinarian) }}"
                               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Edit Profile
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
