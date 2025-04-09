@props(['label', 'icon', 'route'])

<a href="{{ route($route) }}" class="p-6 bg-white rounded-lg shadow hover:bg-blue-100 text-center transition duration-200">
    <div class="text-5xl mb-4">{{ $icon }}</div>
    <div class="text-xl font-semibold">{{ $label }}</div>
</a>
