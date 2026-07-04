@php
    $currentDate = \Carbon\Carbon::parse($date);

    if ($view === 'daily') {
        $prevUrl = route('schedule.index', ['view' => 'daily', 'date' => $currentDate->copy()->subDay()->toDateString()]);
        $nextUrl = route('schedule.index', ['view' => 'daily', 'date' => $currentDate->copy()->addDay()->toDateString()]);
        $todayUrl = route('schedule.index', ['view' => 'daily', 'date' => now()->toDateString()]);
    } elseif ($view === 'weekly') {
        $prevUrl = route('schedule.index', ['view' => 'weekly', 'date' => $currentDate->copy()->subWeek()->toDateString()]);
        $nextUrl = route('schedule.index', ['view' => 'weekly', 'date' => $currentDate->copy()->addWeek()->toDateString()]);
        $todayUrl = route('schedule.index', ['view' => 'weekly', 'date' => now()->toDateString()]);
    } elseif ($view === 'monthly') {
        $prevUrl = route('schedule.index', ['view' => 'monthly', 'date' => $currentDate->copy()->subMonth()->format('Y-m')]);
        $nextUrl = route('schedule.index', ['view' => 'monthly', 'date' => $currentDate->copy()->addMonth()->format('Y-m')]);
        $todayUrl = route('schedule.index', ['view' => 'monthly', 'date' => now()->format('Y-m')]);
    }
@endphp

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Schedule</h1>

    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        {{-- Prev / Today / Next --}}
        <div class="inline-flex rounded-md shadow-sm" role="group">
            <a href="{{ $prevUrl }}" class="px-3 py-2 text-sm font-medium border border-gray-200 rounded-s-lg bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                &lt;
            </a>
            <a href="{{ $todayUrl }}" class="px-3 py-2 text-sm font-medium border-t border-b border-gray-200 bg-white text-blue-600 hover:bg-gray-50 dark:bg-gray-800 dark:text-blue-400 dark:border-gray-600 dark:hover:bg-gray-700">
                Today
            </a>
            <a href="{{ $nextUrl }}" class="px-3 py-2 text-sm font-medium border border-gray-200 rounded-e-lg bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                &gt;
            </a>
        </div>

        {{-- View Toggle --}}
        <div class="inline-flex rounded-md shadow-sm" role="group">
            <a href="{{ route('schedule.index', ['view' => 'daily', 'date' => $date]) }}"
               class="px-4 py-2 text-sm font-medium border border-gray-200 rounded-s-lg {{ $view === 'daily' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700' }}">
                Daily
            </a>
            <a href="{{ route('schedule.index', ['view' => 'weekly', 'date' => $date]) }}"
               class="px-4 py-2 text-sm font-medium border-t border-b border-gray-200 {{ $view === 'weekly' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700' }}">
                Weekly
            </a>
            <a href="{{ route('schedule.index', ['view' => 'monthly', 'date' => $date]) }}"
               class="px-4 py-2 text-sm font-medium border border-gray-200 rounded-e-lg {{ $view === 'monthly' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700' }}">
                Monthly
            </a>
        </div>

        {{-- Date Picker --}}
        <form action="{{ route('schedule.index') }}" method="GET" class="flex items-center gap-2">
            <input type="hidden" name="view" value="{{ $view }}">
            <input type="{{ $inputType ?? 'date' }}" name="date" value="{{ $date }}"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700">Go</button>
        </form>
    </div>
</div>
