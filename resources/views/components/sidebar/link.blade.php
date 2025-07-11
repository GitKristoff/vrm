@props(['active' => false, 'href' => '#'])

<a href="{{ $href }}" @class([
    'block py-2.5 px-4 rounded transition duration-200',
    'bg-gray-700' => $active,
    'hover:bg-gray-700' => !$active
])>
    {{ $slot }}
</a>