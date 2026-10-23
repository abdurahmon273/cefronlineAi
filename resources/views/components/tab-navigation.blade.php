{{-- Tab Navigation Component --}}
<div class="mb-6">
    <nav class="flex space-x-1 bg-gray-100 rounded-lg p-1" aria-label="Tabs">
        @foreach ($tabs as $index => $part_title)
            <button
                data-{{ $identifier }}="{{ $part_title }}"
                class="{{ $buttonClass }} {{ $index === 0
                    ? 'bg-white text-orange-600 shadow-sm'
                    : 'text-gray-500 hover:text-gray-700' }}
                flex-1 whitespace-nowrap rounded-md py-2.5 px-3 text-sm font-medium transition-all duration-200">
                {{ $part_title }}
            </button>
        @endforeach
    </nav>
</div>
