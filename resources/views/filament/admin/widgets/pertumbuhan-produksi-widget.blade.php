@php
    $growthData = $this->getGrowthData();
@endphp

<x-filament-widgets::widget>
    <div class="rounded-xl p-5 bg-[#FFF5E0] h-full flex flex-col gap-4">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <div class="p-2 rounded-md bg-white shadow-sm">
                <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Pertumbuhan Produksi</h2>
        </div>

        {{-- Growth Cards --}}
        <div class="flex gap-3 flex-1">
            @foreach($growthData as $item)
                <div class="flex-1 bg-white rounded-xl p-4 text-center border border-gray-200 shadow-sm flex flex-col items-center justify-center gap-1">
                    <div class="text-[#E07B2A] font-bold text-base leading-tight">{{ $item['label'] }}</div>
                    <div class="text-xs text-gray-600 font-medium">{{ $item['subtitle'] }}</div>
                    <div class="text-xl font-bold text-gray-900 mt-1">{{ $item['value'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- Filters --}}
        <div class="flex items-center gap-3">
            <select
                wire:model.live="selectedQuarter"
                class="flex-1 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg p-2.5 focus:ring-[#E07B2A] focus:border-[#E07B2A] shadow-sm"
            >
                @foreach($availableQuarters as $q)
                    <option value="{{ $q }}">Triwulan {{ $q }}</option>
                @endforeach
            </select>

            <select
                wire:model.live="selectedYear"
                class="flex-1 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg p-2.5 focus:ring-[#E07B2A] focus:border-[#E07B2A] shadow-sm"
            >
                @foreach($availableYears as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>
</x-filament-widgets::widget>
