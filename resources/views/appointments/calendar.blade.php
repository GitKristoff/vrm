<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Appointment Calendar
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{-- @if($events->isEmpty())
                        <div class="text-red-600 font-bold">No appointments found for your account.</div>
                    @else
                        <div class="text-green-600 font-bold">Found {{ $events->count() }} appointments.</div>
                    @endif --}}

                    {{-- <div id="calendar" style="height:600px; width:100%;"></div> --}}
                    <div id="calendar" class="min-h-[500px] w-full"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                timeZone: 'Asia/Manila',
                initialView: 'dayGridMonth',
                events: @json($events),
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                },
                eventContent: function(arg) {
                    // Use a paw icon (SVG) or any icon you like
                    return {
                        html: `<span title="${arg.event.title}" style="cursor:pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#6366f1" viewBox="0 0 24 24" style="vertical-align:middle;">
                                <path d="M12 2C13.1046 2 14 2.89543 14 4C14 5.10457 13.1046 6 12 6C10.8954 6 10 5.10457 10 4C10 2.89543 10.8954 2 12 2ZM4.5 8C5.60457 8 6.5 8.89543 6.5 10C6.5 11.1046 5.60457 12 4.5 12C3.39543 12 2.5 11.1046 2.5 10C2.5 8.89543 3.39543 8 4.5 8ZM19.5 8C20.6046 8 21.5 8.89543 21.5 10C21.5 11.1046 20.6046 12 19.5 12C18.3954 12 17.5 11.1046 17.5 10C17.5 8.89543 18.3954 8 19.5 8ZM7.5 16C8.60457 16 9.5 16.8954 9.5 18C9.5 19.1046 8.60457 20 7.5 20C6.39543 20 5.5 19.1046 5.5 18C5.5 16.8954 6.39543 16 7.5 16ZM16.5 16C17.6046 16 18.5 16.8954 18.5 18C18.5 19.1046 17.6046 20 16.5 20C15.3954 20 14.5 19.1046 14.5 18C14.5 16.8954 15.3954 16 16.5 16ZM12 8C14.2091 8 16 9.79086 16 12C16 14.2091 14.2091 16 12 16C9.79086 16 8 14.2091 8 12C8 9.79086 9.79086 8 12 8Z"/>
                            </svg>
                        </span>`
                    };
                }
            });
            calendar.render();
        });
    </script>

    {{-- <pre>{{ $events->toJson() }}</pre> --}}
</x-app-layout>
