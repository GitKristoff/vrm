{{-- filepath: resources/views/vet/settings.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Veterinarian Settings
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow-md">
            @if(session('success'))
                <div class="mb-6 text-green-700 bg-green-100 border border-green-400 rounded px-4 py-2">
                    {{ session('success') }}
                </div>
            @endif

            {{-- @if($errors->any())
                <div class="mb-4 text-red-700 bg-red-100 border border-red-400 rounded px-4 py-2">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}

            {{-- Current Settings Display --}}
            <div class="mb-8 p-4 bg-gray-50 rounded border border-gray-200">
                <h3 class="font-semibold text-gray-700 mb-2">Your Current Settings</h3>
                <div class="text-sm text-gray-700">
                    <div><strong>Working Days:</strong> {{ $vet->working_days ? implode(', ', $vet->working_days) : 'Not set' }}</div>
                    <div>
                        <strong>Start Time:</strong>
                        {{ $vet->start_time ? \Carbon\Carbon::parse($vet->start_time)->format('g:i A') : 'Not set' }}
                    </div>
                    <div>
                        <strong>End Time:</strong>
                        {{ $vet->end_time ? \Carbon\Carbon::parse($vet->end_time)->format('g:i A') : 'Not set' }}
                    </div>
                    <div><strong>Status:</strong> {{ ucfirst($vet->status) }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('vet.settings.update') }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label class="block font-semibold mb-2 text-gray-700">Working Days</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="working_days[]" value="{{ $day }}"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        {{ in_array($day, $vet->working_days ?? []) ? 'checked' : '' }}>
                                    <span class="ml-2 text-gray-600">{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-semibold mb-2 text-gray-700">Start Time</label>
                            <input type="time" name="start_time" value="{{ $vet->start_time }}"
                                class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-gray-700">End Time</label>
                            <input type="time" name="end_time" value="{{ $vet->end_time }}"
                                class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold mb-2 text-gray-700">Status</label>
                        <select name="status" class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="in" {{ $vet->status == 'in' ? 'selected' : '' }}>In</option>
                            <option value="out" {{ $vet->status == 'out' ? 'selected' : '' }}>Out</option>
                            <option value="on leave" {{ $vet->status == 'on leave' ? 'selected' : '' }}>On Leave</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end mt-8">
                    <x-primary-button>Save Settings</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
