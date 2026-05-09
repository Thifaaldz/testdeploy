<x-filament-panels::page>
    <!-- Main Widget Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @if($activeTab === 'DSI')
            <!-- Left Column -->
            <div class="lg:col-span-5 flex flex-col gap-6">
                @livewire(\App\Filament\Admin\Widgets\TimelineWidget::class)
                @livewire(\App\Filament\Admin\Widgets\DataIndustriChartWidget::class)
            </div>

            <!-- Right Column -->
            <div class="lg:col-span-7 flex flex-col gap-6">
                @livewire(\App\Filament\Admin\Widgets\ProgressDataWidget::class)
                @livewire(\App\Filament\Admin\Widgets\PertumbuhanProduksiWidget::class)
                @livewire(\App\Filament\Admin\Widgets\PeranIndustriChartWidget::class)
            </div>
        @elseif($activeTab === 'IBS')
            <!-- Placeholder for IBS Widgets -->
            <div class="lg:col-span-12 flex flex-col gap-6 items-center justify-center py-12">
                <div class="text-2xl font-bold text-gray-400">Dashboard IBS Sedang Dikembangkan</div>
                <p class="text-gray-500">Menampilkan Peta Provinsi dan Grafik Batang.</p>
            </div>
        @else
            <!-- Placeholder for other tabs -->
            <div class="lg:col-span-12 flex flex-col gap-6 items-center justify-center py-12">
                <div class="text-2xl font-bold text-gray-400">Dashboard {{ $activeTab }}</div>
            </div>
        @endif
    </div>
</x-filament-panels::page>

