@props([
    'route',
    'filters' => [],
    'formId' => 'search-form',
    'exportPermission' => null,
    'types' => [],
    'conditions' => [],
    'locations' => []
])

<div class="bg-gradient-to-b from-gray-50 to-white p-6 border-b">
    <form action="{{ $route }}" method="GET" id="{{ $formId }}">
        <div class="grid grid-cols-1 md:grid-cols-{{ count($filters) > 3 ? '4' : count($filters) }} gap-4">
            @if(in_array('search', $filters))
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="block w-full border border-gray-300 rounded-md pl-10 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition-shadow duration-200"
                        placeholder="Search by name or code...">
                </div>
            </div>
            @endif

            @if(in_array('type', $filters))
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-tag text-gray-400"></i>
                    </div>
                    <select name="type" id="type" class="block w-full border border-gray-300 rounded-md pl-10 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition-shadow duration-200">
                        <option value="">All Types</option>
                        @foreach($types ?? [] as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            @if(in_array('condition', $filters))
            <div>
                <label for="condition" class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-thermometer-half text-gray-400"></i>
                    </div>
                    <select name="condition" id="condition" class="block w-full border border-gray-300 rounded-md pl-10 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition-shadow duration-200">
                        <option value="">All Conditions</option>
                        @foreach($conditions ?? ['Good', 'Fair', 'Poor'] as $condition)
                            <option value="{{ $condition }}" {{ request('condition') == $condition ? 'selected' : '' }}>{{ $condition }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            @if(in_array('location', $filters))
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                    </div>
                    <select name="location" id="location" class="block w-full border border-gray-300 rounded-md pl-10 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition-shadow duration-200">
                        <option value="">All Locations</option>
                        @foreach($locations ?? [] as $location)
                            <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>{{ $location }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            @if(in_array('date_range', $filters))
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                    </div>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                        class="block w-full border border-gray-300 rounded-md pl-10 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition-shadow duration-200">
                </div>
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                    </div>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                        class="block w-full border border-gray-300 rounded-md pl-10 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition-shadow duration-200">
                </div>
            </div>
            @endif

            @if(in_array('status', $filters))
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-info-circle text-gray-400"></i>
                    </div>
                    <select name="status" id="status" class="block w-full border border-gray-300 rounded-md pl-10 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition-shadow duration-200">
                        <option value="">All Statuses</option>
                        @foreach($statuses ?? [] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            {{ $slot ?? '' }}
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
            @if(in_array('per_page', $filters))
            <div class="w-full sm:w-auto">
                <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Items per page</label>
                <select name="per_page" id="per_page" class="border border-gray-300 rounded-md shadow-sm pl-3 pr-8 py-2 bg-white text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition-shadow duration-200"
                    onchange="document.getElementById('{{ $formId }}').submit();">
                    @foreach([10, 25, 50, 100] as $perPage)
                        <option value="{{ $perPage }}" {{ request('per_page', 10) == $perPage ? 'selected' : '' }}>{{ $perPage }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <div></div>
            @endif
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition duration-150 ease-in-out">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="{{ $route }}" class="flex-1 sm:flex-none px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-25 transition duration-150 ease-in-out">
                    <i class="fas fa-redo mr-1"></i> Reset
                </a>
                @if($exportPermission && auth()->user()->can($exportPermission))
                <button type="submit" name="export" value="csv" class="flex-1 sm:flex-none px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition duration-150 ease-in-out">
                    <i class="fas fa-file-csv mr-1"></i> Export
                </button>
                @endif
            </div>
        </div>
    </form>
</div>
