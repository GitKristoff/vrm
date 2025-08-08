<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Pets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            Registered Pets
                        </h3>
                        <a href="{{ route('pets.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150
                           sm:px-4 sm:py-2 px-2 py-1 text-xs">
                            Add New Pet
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($pets->isEmpty())
                        <p class="text-gray-500">No pets registered yet.</p>
                    @else
                        <!-- Mobile Card List -->
                        <div class="sm:hidden space-y-4">
                            @foreach($pets as $pet)
                                <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-between shadow">
                                    <div class="flex items-center">
                                        <div class="h-14 w-14 rounded-full overflow-hidden flex-shrink-0 bg-gray-200">
                                            @if($pet->profile_image)
                                                <img src="{{ asset('storage/'.$pet->profile_image) }}"
                                                    alt="{{ $pet->name }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <span class="flex items-center justify-center h-full w-full text-gray-400 text-xs">No image</span>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="font-semibold text-gray-900">{{ $pet->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $pet->species }} &middot; {{ $pet->breed ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $pet->age }} years</div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2 ml-2">
                                        <a href="{{ route('pets.edit', $pet->id) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">Edit</a>
                                        <form action="{{ route('pets.destroy', $pet) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="text-red-600 hover:text-red-900 text-xs font-medium"
                                                onclick="confirmAction(event, 'Are you sure you want to delete this pet? All associated appointments will be cancelled.')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Desktop Table -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Species</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Breed</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pets as $pet)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pet->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pet->species }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pet->breed ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pet->age }} years</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($pet->profile_image)
                                                <div class="w-14 h-14 rounded-full overflow-hidden">
                                                    <img src="{{ asset('storage/'.$pet->profile_image) }}"
                                                        alt="{{ $pet->name }}"
                                                        class="w-full h-full object-cover">
                                                </div>
                                            @else
                                                <span class="text-gray-400">No image</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('pets.edit', $pet->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            <form action="{{ route('pets.destroy', $pet) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="text-red-600 hover:text-red-900"
                                                    onclick="confirmAction(event, 'Are you sure you want to delete this pet? All associated appointments will be cancelled.')">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
