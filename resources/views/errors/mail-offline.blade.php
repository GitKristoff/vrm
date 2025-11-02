<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Connection problem</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <div class="flex items-start space-x-4">
                    <div class="text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l5.518 9.809c.75 1.333-.213 2.992-1.742 2.992H4.481c-1.53 0-2.492-1.66-1.742-2.992L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-7a1 1 0 01.993.883L11 7v4a1 1 0 01-1.993.117L9 11V7a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Oops — internet connection required</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            The application could not send email because it appears you are offline or the mail server is unreachable.
                            This affects actions like account verification, password reset, and appointment notification emails.
                        </p>

                        <ul class="mt-4 text-sm text-gray-600 list-disc list-inside">
                            <li>Check your internet connection and try again.</li>
                            <li>If you are behind a firewall or proxy, ensure SMTP access is allowed.</li>
                            <li>You can retry the action (resend verification/reset password) once online.</li>
                        </ul>

                        <div class="mt-6 flex gap-3">
                            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border rounded text-sm">Go back</a>
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded text-sm">Return home</a>
                        </div>

                        <p class="mt-4 text-xs text-gray-400">Error shown when mail transport failed (SMTP or network). If this happens often, consider configuring a queued mail driver or a fallback notification channel.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
