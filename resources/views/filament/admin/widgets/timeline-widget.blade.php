@php
    use Carbon\Carbon;
    $now         = Carbon::now();
    $currentYear = $now->year;
    $currentMonth = $now->month;
    $currentDay  = $now->day;

    // Get start of calendar month for grid
    $firstDayOfMonth = Carbon::createFromDate($currentYear, $currentMonth, 1);
    $startDow = $firstDayOfMonth->dayOfWeekIso; // 1=Mon, 7=Sun
    $daysInMonth = $firstDayOfMonth->daysInMonth;
    $daysInPrevMonth = $firstDayOfMonth->copy()->subMonth()->daysInMonth;

    $weeks  = [];
    $day    = 1;
    $prevDay = $daysInPrevMonth - $startDow + 2;
    $nextDay = 1;

    for ($week = 0; $week < 6; $week++) {
        $row = [];
        for ($dow = 1; $dow <= 7; $dow++) {
            $cellIndex = $week * 7 + $dow;
            if ($cellIndex < $startDow) {
                $row[] = ['day' => $prevDay++, 'type' => 'prev'];
            } elseif ($day <= $daysInMonth) {
                $row[] = ['day' => $day++, 'type' => 'current'];
            } else {
                $row[] = ['day' => $nextDay++, 'type' => 'next'];
            }
        }
        $weeks[] = $row;
        if ($day > $daysInMonth && $week >= 3) break;
    }

    $monthName = $firstDayOfMonth->translatedFormat('M Y');
@endphp

<x-filament-widgets::widget>
    <div class="rounded-xl p-5 bg-[#FFF5E0] h-full flex flex-col gap-4">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <div class="p-2 rounded-md bg-white shadow-sm">
                <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Timeline Kegiatan Statistik Industri</h2>
        </div>

        {{-- Calendar --}}
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            {{-- Month Navigation --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-1 font-bold text-gray-800 text-sm">
                    {{ $monthName }}
                    <svg class="w-3.5 h-3.5 text-[#E07B2A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <div class="flex gap-1">
                    <button class="p-1 rounded hover:bg-gray-100 text-[#E07B2A]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button class="p-1 rounded hover:bg-gray-100 text-[#E07B2A]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Day Names --}}
            <div class="grid grid-cols-7 gap-0.5 mb-1">
                @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $d)
                    <div class="text-center text-[10px] font-semibold text-gray-400 py-1">{{ $d }}</div>
                @endforeach
            </div>

            {{-- Calendar Days --}}
            @foreach($weeks as $week)
                <div class="grid grid-cols-7 gap-0.5">
                    @foreach($week as $cell)
                        @php
                            $isToday   = $cell['type'] === 'current' && $cell['day'] === $currentDay;
                            $isCurrent = $cell['type'] === 'current';
                        @endphp
                        <div class="text-center py-1">
                            <span class="
                                text-xs inline-flex items-center justify-center w-6 h-6 rounded-full
                                {{ $isToday ? 'bg-[#E07B2A] text-white font-bold' : '' }}
                                {{ !$isToday && $isCurrent ? 'text-gray-800 hover:bg-orange-50' : 'text-gray-300' }}
                                cursor-pointer
                            ">
                                {{ $cell['day'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
