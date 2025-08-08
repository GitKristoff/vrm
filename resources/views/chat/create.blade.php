<x-app-layout>
    <div class="max-w-lg mx-auto py-12">
        <h2 class="text-xl font-bold mb-4">Start a New Conversation</h2>
        <form action="{{ route('chat.storeConversation') }}" method="POST">
            @csrf
            <label for="user_id" class="block mb-2">Select a user:</label>
            <select name="user_id" id="user_id" class="w-full mb-4 border rounded px-2 py-2">
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Start Chat</button>
                <button type="button" onclick="window.history.back();" class="bg-gray-300 text-gray-800 px-4 py-2 rounded">Cancel</button>
            </div>
        </form>
    </div>
</x-app-layout>
