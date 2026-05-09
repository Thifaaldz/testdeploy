@php
    $totals = $this->getTotals();
    $stats = [
        ['label' => 'Selesai Cacah', 'value' => $totals['selesai_cacah']],
        ['label' => 'Sisa Target',   'value' => $totals['sisa_target']],
        ['label' => 'Eligible',      'value' => $totals['eligible']],
        ['label' => 'Sedang Cacah',  'value' => $totals['sedang_cacah']],
        ['label' => 'Kondisi Data',  'value' => $totals['kondisi_data']],
    ];
@endphp

<x-filament-widgets::widget>
    <div class="rounded-xl p-5 bg-[#FFF5E0] h-full flex flex-col gap-4">
        {{-- Header --}}
        <div class="flex items-center gap-3">
            <div class="p-2 rounded-full bg-gray-900">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Progress Pemasukan Data</h2>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 gap-3 flex-1">
            @foreach($stats as $stat)
                <div class="rounded-lg bg-gray-100 p-4 text-center flex flex-col items-center justify-center">
                    <div class="text-[#E07B2A] font-semibold text-xs mb-1">{{ $stat['label'] }}</div>
                    <div class="text-2xl font-bold text-gray-800">
                        {{ number_format($stat['value']) }}
                    </div>
                </div>
            @endforeach

            {{-- Year Filter --}}
            <div class="rounded-lg bg-white border border-gray-200 p-3 flex items-center justify-center">
                <select
                    wire:model.live="selectedYear"
                    class="w-full text-sm text-gray-700 bg-transparent border-none focus:ring-0 font-semibold cursor-pointer text-center"
                >
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
