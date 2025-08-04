<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="mt-4 flex items-start">
            <div class="mr-6 flex flex-col items-center"
                 x-data="{
                    preview: '{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : '' }}',
                    filename: '',
                    updatePreview(event) {
                        const file = event.target.files[0];
                        if (file) {
                            this.filename = file.name;
                            const reader = new FileReader();
                            reader.onload = e => this.preview = e.target.result;
                            reader.readAsDataURL(file);
                        }
                    }
                 }"
            >
                <x-input-label for="profile_picture" :value="__('Profile Picture')" class="mb-2" />

                <div class="relative inline-block group">
                    <template x-if="preview">
                        <img :src="preview" alt="Profile Preview"
                             class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg transition duration-200 group-hover:brightness-90" />
                    </template>
                    <template x-if="!preview">
                        <div class="h-32 w-32 rounded-full bg-gray-200 flex items-center justify-center border-4 border-white shadow-lg">
                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                    </template>

                    <label for="profile_picture"
                        class="absolute bottom-2 right-2 bg-indigo-600 rounded-full p-1 cursor-pointer border border-white shadow hover:bg-indigo-700 transition group-hover:scale-110">
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2l-6 6m-2 2l-6-6" />
                        </svg>
                    </label>
                </div>

                <input id="profile_picture" type="file" name="profile_picture" class="hidden"
                       accept="image/*" x-on:change="updatePreview">

                <template x-if="filename">
                    <div class="mt-2 text-xs text-gray-500">
                        Selected: <span class="font-semibold" x-text="filename"></span>
                    </div>
                </template>
            </div>
            <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
        </div>


        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>


        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="fixed top-6 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
                >
                    Profile picture updated successfully!
                </div>
            @endif
        </div>
    </form>

    <div class="flex items-center gap-4 mt-6">
        @if($user->profile_picture)
            <form method="POST" action="{{ route('profile.remove-picture') }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-pink-500 text-white font-semibold rounded-lg shadow-md hover:from-red-600 hover:to-pink-600 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400"
                >
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Remove Profile Picture
                </button>
            </form>
        @endif
    </div>
</section>
