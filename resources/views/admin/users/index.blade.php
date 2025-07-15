<x-app-layout>
    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">

                @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                @if(session('error'))
                    <div class="mb-4 px-4 py-3 bg-red-100 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium">System Users</h3>
                    <a href="{{ route('admin.veterinarians.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Add Veterinarian
                    </a>
                </div>

                @include('admin.users.partials.users-table', ['users' => $users])
            </div>
        </div>
    </div>
</div>
</x-app-layout>
