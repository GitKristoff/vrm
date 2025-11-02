<?php
$currentAdminId = auth()->id();
?>

<!-- Mobile: Card layout -->
<div class="sm:hidden space-y-4">
    @foreach($users as $user)
        <div class="bg-white rounded-lg shadow p-4 flex flex-col gap-3">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden border-2 border-white shadow">
                    @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" class="h-10 w-10 object-cover rounded-full" />
                    @else
                        <span class="text-gray-600 font-medium">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div>
                    <div class="text-base font-semibold text-gray-900">{{ $user->name }}</div>
                    <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                </div>
            </div>
            <div class="flex flex-col gap-2 mt-2">
                <span class="flex items-center gap-1 text-gray-500 text-sm">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    {{ $user->email }}
                </span>
                <div class="flex gap-2">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : ($user->role === 'veterinarian' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                    @if($user->is_active)
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Disabled</span>
                    @endif
                </div>
            </div>
            <div class="flex gap-4 mt-3 border-t pt-3">
                @if($user->role === 'veterinarian')
                    <a href="{{ route('admin.veterinarians.edit', $user->veterinarian) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                    </a>
                @endif
                <a href="{{ route('admin.users.show', $user) }}" class="text-gray-600 hover:text-gray-900" title="View">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                </a>

                {{-- Enable / Disable action (mobile & small icon kept for spacing) --}}
                @php
                    $cannotToggle = $user->role === 'admin' || $user->id === $currentAdminId;
                    $toggleTitle = $cannotToggle
                        ? ($user->role === 'admin' ? 'Cannot modify system admin' : 'Cannot disable your own account')
                        : ($user->is_active ? 'Disable user' : 'Enable user');
                @endphp

                <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        @if($cannotToggle) disabled @endif
                        class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium focus:outline-none
                            @if($cannotToggle)
                                text-gray-400 bg-gray-100 cursor-not-allowed
                            @else
                                {{ $user->is_active ? 'bg-yellow-50 text-yellow-800 hover:bg-yellow-100' : 'bg-green-50 text-green-800 hover:bg-green-100' }}
                            @endif"
                        title="{{ $toggleTitle }}"
                        onclick="confirmAction(event, '{{ $cannotToggle ? $toggleTitle : ($user->is_active ? 'Are you sure you want to disable this user? The account will be blocked but data will remain.' : 'Are you sure you want to enable this user?') }}')"
                    >
                        @if($user->is_active)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10 18a8 8 0 110-16 8 8 0 010 16zm1-11V5a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0V9h2a1 1 0 100-2h-2z" />
                            </svg>
                            <span class="sr-only">Disable</span>
                            <span class="hidden sm:inline">Disable</span>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M2 10a8 8 0 1116 0A8 8 0 012 10zm8-4a1 1 0 100 2 1 1 0 000-2zm1 8H7a1 1 0 000 2h4a1 1 0 000-2z" />
                            </svg>
                            <span class="sr-only">Enable</span>
                            <span class="hidden sm:inline">Enable</span>
                        @endif
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Desktop: Table layout -->
<div class="hidden sm:block overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden border-2 border-white shadow">
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" class="h-10 w-10 object-cover rounded-full" />
                                @else
                                    <span class="text-gray-600 font-medium">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex items-center">
                            <svg class="h-4 w-4 text-gray-400 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ $user->email }}
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' :
                               ($user->role === 'veterinarian' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($user->is_active)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">
                                Disabled
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            @if($user->role === 'veterinarian')
                                <a href="{{ route('admin.veterinarians.edit', $user->veterinarian) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </a>
                            @else
                                <span class="text-indigo-300 cursor-not-allowed" title="Edit not available for this role">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </span>
                            @endif
                            <a href="{{ route('admin.users.show', $user) }}" class="text-gray-600 hover:text-gray-900" title="View">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            {{-- <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete" onclick="confirmAction(event, 'Are you sure you want to delete this user? All associated data will be permanently deleted.')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form> --}}
                            <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    @if($cannotToggle) disabled @endif
                                    class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium focus:outline-none
                                        @if($cannotToggle)
                                            text-gray-400 bg-gray-100 cursor-not-allowed
                                        @else
                                            {{ $user->is_active ? 'bg-yellow-50 text-yellow-800 hover:bg-yellow-100' : 'bg-green-50 text-green-800 hover:bg-green-100' }}
                                        @endif"
                                    title="{{ $toggleTitle }}"
                                    onclick="confirmAction(event, '{{ $cannotToggle ? $toggleTitle : ($user->is_active ? 'Are you sure you want to disable this user? The account will be blocked but data will remain.' : 'Are you sure you want to enable this user?') }}')"
                                >
                                    @if($user->is_active)
                                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M10 18a8 8 0 110-16 8 8 0 010 16zm1-11V5a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0V9h2a1 1 0 100-2h-2z" />
                                        </svg> --}}
                                        <span class="hidden sm:inline">Disable</span>
                                    @else
                                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M2 10a8 8 0 1116 0A8 8 0 012 10zm8-4a1 1 0 100 2 1 1 0 000-2zm1 8H7a1 1 0 000 2h4a1 1 0 000-2z" />
                                        </svg> --}}
                                        <span class="hidden sm:inline">Enable</span>
                                    @endif
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{-- {{ $users->links() }} --}}
</div>
