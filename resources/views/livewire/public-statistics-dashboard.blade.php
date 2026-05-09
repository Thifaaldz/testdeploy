@php
    $activeQuarter = $selectedQuarter === 'latest' ? 'Periode terbaru' : 'Q' . $selectedQuarter;
    $activeYear = $selectedYear === 'all' ? 'Lintas tahun' : $selectedYear;
    $mapLayerCount = count($mapPayload['geoJsonLayers']);
    $mapLocations = collect($mapPayload['locations'])->take(4);
    $insightCollection = collect($insightStats);
    $sourceCount = data_get($insightCollection->firstWhere('label', 'Sumber aktif'), 'value', 0);
    $locationCount = data_get($insightCollection->firstWhere('label', 'Lokasi industri'), 'value', 0);
    $geoJsonCount = data_get($insightCollection->firstWhere('label', 'Layer GeoJSON'), 'value', 0);
    $updateLabel = data_get($insightCollection->firstWhere('label', 'Update terakhir'), 'value', 'Belum tersedia');
    $progressCompletion = $selectedProgress?->completion_percentage ?? 0;
    $sourceTypeLabel = $latestSource?->source_type
        ? str($latestSource->source_type)->replace(['_', '-'], ' ')->title()->toString()
        : 'Belum tersedia';
    $sourceImportedAt = $latestSource?->last_imported_at
        ? $latestSource->last_imported_at->format('d M Y H:i') . ' WIB'
        : 'Belum diimpor';
    $sourceStatus = $latestSource?->status
        ? str($latestSource->status)->replace(['_', '-'], ' ')->title()->toString()
        : 'Seed awal';
    $progressUpdatedAt = $selectedProgress?->updated_at
        ? $selectedProgress->updated_at->format('d M Y H:i') . ' WIB'
        : 'Belum tersedia';
    $bpsLogoUrl = asset('build/images/image.png');
    $heroCards = collect($kpis)
        ->map(fn (array $metric) => [
            'label' => $metric['label'],
            'value' => $metric['formatted'],
            'meta' => $metric['period'] ?? 'Periode terbaru',
        ])
        ->merge(collect($shareCards)->map(fn (array $metric) => [
            'label' => $metric['label'],
            'value' => $metric['formatted'],
            'meta' => $metric['period'] ?? 'Periode terbaru',
        ]))
        ->merge([
            [
                'label' => 'Lokasi Industri',
                'value' => number_format($locationCount),
                'meta' => 'Titik industri terpetakan',
            ],
            [
                'label' => 'Layer GeoJSON',
                'value' => number_format($mapLayerCount),
                'meta' => 'Layer wilayah aktif',
            ],
            [
                'label' => 'Progres Survei',
                'value' => number_format($progressCompletion, 1) . '%',
                'meta' => $selectedProgress?->activity_name ?? 'Belum tersedia',
            ],
        ])
        ->take(5)
        ->values();
    $subjectColumns = [
        [
            'title' => 'Statistik Industri',
            'items' => $categories->map(fn ($navCategory) => [
                'active' => $selectedCategory === $navCategory->code,
                'code' => $navCategory->code,
                'meta' => $navCategory->description ?: 'Indikator resmi kategori ' . $navCategory->code,
                'title' => $navCategory->name,
            ])->all(),
        ],
        [
            'title' => 'Parameter Dashboard',
            'items' => [
                ['title' => 'Periode Data', 'meta' => $activeQuarter . ' · ' . $activeYear],
                ['title' => 'Sumber Data', 'meta' => $latestSource?->name ?? 'Belum ada sumber'],
                ['title' => 'Status Impor', 'meta' => $sourceStatus],
                ['title' => 'Seri Grafik', 'meta' => count($trendChart['datasets']) . ' seri aktif'],
                ['title' => 'Update Terakhir', 'meta' => $sourceImportedAt],
            ],
        ],
        [
            'title' => 'Layanan Analitik',
            'items' => [
                ['title' => 'Grafik Tren', 'meta' => $trendChart['title']],
                ['title' => 'Distribusi Sektor', 'meta' => $distributionChart['subtitle']],
                ['title' => 'Peta Industri', 'meta' => number_format($locationCount) . ' titik dan ' . number_format($mapLayerCount) . ' layer'],
                ['title' => 'Progres Kegiatan', 'meta' => $progressItems->count() . ' kegiatan statistik'],
                ['title' => 'Sumber Aktif', 'meta' => number_format($sourceCount) . ' dataset terkelola'],
            ],
        ],
    ];
    $informationTabs = [
        ['href' => '#statistik-utama', 'label' => 'Tabel Statistik', 'active' => true],
        ['href' => '#progres', 'label' => 'Progres Survei', 'active' => false],
        ['href' => '#grafik', 'label' => 'Grafik Tren', 'active' => false],
        ['href' => '#peta', 'label' => 'Peta Industri', 'active' => false],
    ];
    $informationRows = [
        [
            'href' => '#statistik-utama',
            'meta' => $activeQuarter . ' · ' . $activeYear,
            'title' => 'Ringkasan indikator utama kategori ' . $category->name,
        ],
        [
            'href' => '#progres',
            'meta' => $progressUpdatedAt,
            'title' => 'Pemantauan kegiatan ' . ($selectedProgress?->activity_name ?? 'survei statistik industri'),
        ],
        [
            'href' => '#grafik',
            'meta' => count($trendChart['datasets']) . ' seri statistik',
            'title' => $trendChart['title'] . ' untuk pembacaan perkembangan data',
        ],
        [
            'href' => '#grafik',
            'meta' => $distributionChart['subtitle'],
            'title' => $distributionChart['title'] . ' dalam bentuk komposisi visual',
        ],
        [
            'href' => '#peta',
            'meta' => number_format($locationCount) . ' lokasi industri · ' . number_format($geoJsonCount) . ' layer',
            'title' => 'Sebaran lokasi industri dan layer GeoJSON pada peta interaktif',
        ],
    ];
    $trendLabels = collect($trendChart['datasets'])->pluck('label')->take(4)->all();
@endphp

<div class="bps-portal min-h-screen">
    <header class="bps-header">
        <div class="bps-topbar">
            <div class="bps-brand">
                <img src="{{ $bpsLogoUrl }}" alt="Logo Badan Pusat Statistik" class="bps-brand__logo">
                <div>
                    <div class="bps-brand__title">BADAN PUSAT STATISTIK</div>
                    <div class="bps-brand__subtitle">Portal Statistik Industri Indonesia</div>
                </div>
            </div>

            <nav class="bps-main-nav">
                <a href="#beranda" class="bps-main-nav__item bps-main-nav__item--active">Beranda</a>
                <a href="#statistik-utama" class="bps-main-nav__item">Statistik</a>
                <a href="#grafik" class="bps-main-nav__item">Grafik</a>
                <a href="#progres" class="bps-main-nav__item">Progres</a>
                <a href="#peta" class="bps-main-nav__item">Peta</a>
            </nav>

            <div class="bps-topbar__tools">
                @if (Route::has('filament.admin.auth.login'))
                    <a href="{{ route('filament.admin.auth.login') }}" class="bps-tool-chip">Admin</a>
                @endif
                <span class="bps-tool-chip">ID</span>
            </div>
        </div>

        <div class="bps-alert-bar">
            <div class="bps-alert-bar__inner">
                <p class="bps-alert-bar__text">
                    Dalam rangka penyajian statistik industri yang terpercaya, dashboard ini menampilkan indikator resmi,
                    progres kegiatan statistik, grafik perkembangan data, dan peta industri dalam satu portal publik.
                </p>
                <div class="bps-alert-bar__pager">
                    <span>1/1</span>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-[1560px] px-4 pb-12 sm:px-6 lg:px-8">
        <section id="beranda" class="bps-hero stats-reveal">
            <div class="bps-hero__shape bps-hero__shape--left"></div>
            <div class="bps-hero__shape bps-hero__shape--right"></div>

            <div class="bps-hero__content">
                <span class="bps-hero__eyebrow">Statistik Industri Resmi</span>
                <h1 class="bps-hero__title">
                    Statistik industri yang terintegrasi, terpercaya, dan siap dipakai untuk pembacaan data, monitoring kegiatan, dan analisis spasial
                </h1>
                <p class="bps-hero__description">
                    {{ $viewConfig['description'] }}
                    Tampilan halaman ini disusun ulang mengikuti pola visual portal BPS pada contoh yang Anda kirim,
                    namun isi utamanya tetap berfokus pada statistik industri, chart, progress, dan map milik sistem Anda.
                </p>

                <div class="bps-search">
                    <input
                        type="text"
                        class="bps-search__input"
                        placeholder="Cari data statistik industri, kategori, atau indikator..."
                        aria-label="Cari data statistik industri"
                    >
                    <button type="button" class="bps-search__button">Cari</button>
                </div>

                <div class="bps-hero__filters">
                    <label class="bps-inline-filter">
                        <span class="bps-inline-filter__label">Tahun</span>
                        <select wire:model.live="selectedYear" class="bps-inline-filter__input">
                            <option value="all">Semua Tahun</option>
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="bps-inline-filter">
                        <span class="bps-inline-filter__label">Periode</span>
                        <select wire:model.live="selectedQuarter" class="bps-inline-filter__input">
                            <option value="latest">Periode Terbaru</option>
                            <option value="1">Q1</option>
                            <option value="2">Q2</option>
                            <option value="3">Q3</option>
                            <option value="4">Q4</option>
                        </select>
                    </label>

                    <span class="bps-hero__badge">{{ $category->code }}</span>
                    <span class="bps-hero__badge">{{ $updateLabel }}</span>
                </div>

                <div class="bps-category-row">
                    @foreach ($categories as $navCategory)
                        <button
                            type="button"
                            wire:click="setCategory('{{ $navCategory->code }}')"
                            class="bps-category-chip {{ $selectedCategory === $navCategory->code ? 'bps-category-chip--active' : '' }}"
                        >
                            <span class="bps-category-chip__code">{{ $navCategory->code }}</span>
                            <span class="bps-category-chip__name">{{ $navCategory->name }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bps-stats-stage stats-reveal" style="--reveal-delay: .06s;">
            <div class="bps-stats-stage__grid">
                @foreach ($heroCards as $card)
                    <article class="bps-stat-card">
                        <span class="bps-stat-card__label">{{ $card['label'] }}</span>
                        <strong class="bps-stat-card__value">{{ $card['value'] }}</strong>
                        <span class="bps-stat-card__meta">{{ $card['meta'] }}</span>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="bps-shell stats-reveal" style="--reveal-delay: .12s;">
            <div class="bps-shell__cap">Statistik menurut Subjek</div>

            <div class="bps-topics-grid">
                @foreach ($subjectColumns as $column)
                    <article class="bps-topic-card">
                        <h2 class="bps-topic-card__title">{{ $column['title'] }}</h2>

                        <div class="bps-topic-list">
                            @foreach ($column['items'] as $item)
                                @if (isset($item['code']))
                                    <button
                                        type="button"
                                        wire:click="setCategory('{{ $item['code'] }}')"
                                        class="bps-topic-item {{ $item['active'] ? 'bps-topic-item--active' : '' }}"
                                    >
                                        <span class="bps-topic-item__text">
                                            <strong>{{ $item['title'] }}</strong>
                                            <small>{{ $item['meta'] }}</small>
                                        </span>
                                        <span class="bps-topic-item__arrow">&rarr;</span>
                                    </button>
                                @else
                                    <div class="bps-topic-item">
                                        <span class="bps-topic-item__text">
                                            <strong>{{ $item['title'] }}</strong>
                                            <small>{{ $item['meta'] }}</small>
                                        </span>
                                        <span class="bps-topic-item__arrow">&rarr;</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="bps-news stats-reveal" style="--reveal-delay: .18s;">
            <div class="bps-news__head">
                <div>
                    <h2 class="bps-news__title">Informasi Terbaru</h2>
                    <p class="bps-news__subtitle">Ringkasan cepat menuju indikator utama, progres survei, grafik statistik, dan peta industri.</p>
                </div>

                <a href="#peta" class="bps-news__link">Lihat Peta Industri</a>
            </div>

            <div class="bps-news__tabs">
                @foreach ($informationTabs as $tab)
                    <a href="{{ $tab['href'] }}" class="bps-news-tab {{ $tab['active'] ? 'bps-news-tab--active' : '' }}">
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="bps-news__list">
                @foreach ($informationRows as $index => $row)
                    <a href="{{ $row['href'] }}" class="bps-news-item">
                        <span class="bps-news-item__icon">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="bps-news-item__text">
                            <strong>{{ $row['title'] }}</strong>
                            <small>{{ $row['meta'] }}</small>
                        </span>
                        <span class="bps-news-item__arrow">&rsaquo;</span>
                    </a>
                @endforeach
            </div>
        </section>

        <div class="bps-dashboard-grid">
            <section id="statistik-utama" class="bps-card stats-reveal" style="--reveal-delay: .24s;">
                <div class="bps-card__head">
                    <div>
                        <span class="bps-card__kicker">Statistik Utama</span>
                        <h3 class="bps-card__title">Angka Kunci {{ $category->name }}</h3>
                        <p class="bps-card__description">
                            Indikator utama tetap menjadi fokus utama halaman, disusun dalam format yang lebih dekat dengan template BPS.
                        </p>
                    </div>
                    <span class="bps-card__badge">{{ $category->code }}</span>
                </div>

                <div class="bps-kpi-grid">
                    @forelse ($kpis as $metric)
                        <article class="bps-kpi-card">
                            <span class="bps-kpi-card__label">{{ $metric['label'] }}</span>
                            <strong class="bps-kpi-card__value">{{ $metric['formatted'] }}</strong>
                            <span class="bps-kpi-card__meta">{{ $metric['period'] ?? 'Periode terbaru' }}</span>
                        </article>
                    @empty
                        <article class="bps-empty-state">
                            Belum ada indikator utama yang tersedia pada kategori ini.
                        </article>
                    @endforelse
                </div>

                @if ($shareCards !== [])
                    <div class="bps-share-band">
                        <div class="bps-share-band__head">
                            <div>
                                <span class="bps-card__kicker bps-card__kicker--light">Statistik Komparatif</span>
                                <h4 class="bps-share-band__title">Share KEK/KI terhadap Industri Pengolahan</h4>
                            </div>
                            <span class="bps-card__badge bps-card__badge--light">{{ $activeQuarter }}</span>
                        </div>

                        <div class="bps-share-grid">
                            @foreach ($shareCards as $card)
                                <article class="bps-share-card">
                                    <span class="bps-share-card__label">{{ $card['label'] }}</span>
                                    <strong class="bps-share-card__value">{{ $card['formatted'] }}</strong>
                                    <span class="bps-share-card__meta">{{ $card['period'] ?? 'Periode terbaru' }}</span>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

            <aside id="progres" class="bps-card bps-card--blue stats-reveal" style="--reveal-delay: .3s;">
                <div class="bps-card__head">
                    <div>
                        <span class="bps-card__kicker bps-card__kicker--light">Monitoring Kegiatan</span>
                        <h3 class="bps-card__title bps-card__title--light">Progres Survei dan Kondisi Lapangan</h3>
                        <p class="bps-card__description bps-card__description--light">
                            Panel progres dipertahankan sepenuhnya dan diposisikan seperti blok pemantauan resmi pada template portal BPS.
                        </p>
                    </div>
                </div>

                @if ($progressItems->isNotEmpty())
                    <label class="bps-progress-filter">
                        <span class="bps-progress-filter__label">Pilih kegiatan</span>
                        <select wire:model.live="selectedActivity" class="bps-progress-filter__input">
                            @foreach ($progressItems as $progressItem)
                                <option value="{{ $progressItem->activity_name }}">{{ $progressItem->activity_name }}</option>
                            @endforeach
                        </select>
                    </label>

                    @if ($selectedProgress)
                        <div class="bps-progress-overview">
                            <div class="bps-progress-ring" style="--progress: {{ min(100, $progressCompletion) }};">
                                <div class="bps-progress-ring__inner">
                                    <span class="bps-progress-ring__label">Selesai</span>
                                    <strong class="bps-progress-ring__value">{{ number_format($progressCompletion, 1) }}%</strong>
                                    <small class="bps-progress-ring__meta">{{ $selectedProgress->category?->code }}</small>
                                </div>
                            </div>

                            <div class="bps-progress-grid">
                                <article class="bps-progress-stat">
                                    <span>Target awal</span>
                                    <strong>{{ number_format($selectedProgress->target_awal) }}</strong>
                                </article>
                                <article class="bps-progress-stat">
                                    <span>Selesai cacah</span>
                                    <strong>{{ number_format($selectedProgress->selesai_dicacah) }}</strong>
                                </article>
                                <article class="bps-progress-stat">
                                    <span>Eligible</span>
                                    <strong>{{ number_format($selectedProgress->eligible) }}</strong>
                                </article>
                                <article class="bps-progress-stat">
                                    <span>Sedang dicacah</span>
                                    <strong>{{ number_format($selectedProgress->sedang_dicacah) }}</strong>
                                </article>
                            </div>
                        </div>

                        <div class="bps-progress-meta">
                            <div class="bps-progress-meta__item">
                                <span>Kondisi data</span>
                                <strong>{{ $selectedProgress->condition_label }}</strong>
                            </div>
                            <div class="bps-progress-meta__item">
                                <span>Sisa target</span>
                                <strong>{{ number_format($selectedProgress->sisa_target) }}</strong>
                            </div>
                            <div class="bps-progress-meta__item">
                                <span>Pembaruan</span>
                                <strong>{{ $progressUpdatedAt }}</strong>
                            </div>
                            <div class="bps-progress-meta__item">
                                <span>Tipe sumber</span>
                                <strong>{{ $sourceTypeLabel }}</strong>
                            </div>
                        </div>
                    @endif
                @endif
            </aside>
        </div>

        <div id="grafik" class="bps-visual-grid" wire:key="visuals-{{ $selectedCategory }}-{{ $selectedYear }}-{{ $selectedQuarter }}-{{ $selectedActivity }}">
            <section class="bps-card stats-reveal" style="--reveal-delay: .36s;">
                <div class="bps-card__head">
                    <div>
                        <span class="bps-card__kicker">Grafik Tren</span>
                        <h3 class="bps-card__title">{{ $trendChart['title'] }}</h3>
                        <p class="bps-card__description">
                            Grafik tren tetap menjadi komponen utama untuk membaca perubahan antar periode tanpa mengubah fungsi filter kategori dan waktu.
                        </p>
                    </div>
                    <span class="bps-card__badge">{{ count($trendChart['datasets']) }} seri</span>
                </div>

                <div x-data="statisticsVisuals({ trend: @js($trendChart), distribution: @js($distributionChart), map: @js($mapPayload) })" x-init="init()" class="mt-6">
                    <div class="relative h-[380px]" wire:ignore>
                        <canvas x-ref="trendCanvas"></canvas>
                    </div>
                </div>

                @if ($trendLabels !== [])
                    <div class="bps-tag-row">
                        @foreach ($trendLabels as $label)
                            <span class="bps-tag">{{ $label }}</span>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="bps-card stats-reveal" style="--reveal-delay: .42s;">
                <div class="bps-card__head">
                    <div>
                        <span class="bps-card__kicker">Komposisi Statistik</span>
                        <h3 class="bps-card__title">{{ $distributionChart['title'] }}</h3>
                        <p class="bps-card__description">{{ $distributionChart['subtitle'] }}</p>
                    </div>
                    @if ($distributionChart['highlight'])
                        <span class="bps-card__badge">
                            C {{ number_format($distributionChart['highlight'], 2, ',', '.') }}
                        </span>
                    @endif
                </div>

                <div x-data="statisticsVisuals({ trend: @js($trendChart), distribution: @js($distributionChart), map: @js($mapPayload) })" x-init="init()" class="mt-6">
                    <div class="relative h-[360px]" wire:ignore>
                        <canvas x-ref="distributionCanvas"></canvas>
                    </div>
                </div>

                <div class="bps-distribution-list">
                    @foreach ($distributionChart['labels'] as $index => $label)
                        <div class="bps-distribution-item">
                            <span>{{ $label }}</span>
                            <strong>{{ number_format($distributionChart['values'][$index] ?? 0, 2, ',', '.') }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <section id="peta" class="bps-card bps-card--map stats-reveal" style="--reveal-delay: .48s;">
            <div class="bps-card__head">
                <div>
                    <span class="bps-card__kicker">OpenStreetMap dan GeoJSON</span>
                    <h3 class="bps-card__title">Peta Sebaran Lokasi Industri</h3>
                    <p class="bps-card__description">
                        Peta tetap menjadi bagian utama dashboard dan disajikan dengan struktur visual yang lebih dekat ke portal BPS.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="bps-card__badge">{{ number_format($locationCount) }} lokasi</span>
                    <span class="bps-card__badge">{{ number_format($mapLayerCount) }} layer</span>
                </div>
            </div>

            <div class="bps-map-grid">
                <div class="bps-map-stage" x-data="statisticsVisuals({ trend: @js($trendChart), distribution: @js($distributionChart), map: @js($mapPayload) })" x-init="init()">
                    <div class="bps-map-stage__overlay">
                        <span class="bps-card__kicker bps-card__kicker--light">Pemantauan Spasial</span>
                        <strong>Sebaran lokasi industri berdasarkan data kategori aktif</strong>
                        <small>
                            Marker menampilkan titik industri dan layer GeoJSON membantu membaca wilayah pendukung secara spasial.
                        </small>
                    </div>

                    <div class="h-[500px] overflow-hidden rounded-[28px] border border-slate-200" wire:ignore x-ref="mapCanvas"></div>
                </div>

                <aside class="bps-map-aside">
                    <article class="bps-map-info">
                        <div class="bps-map-info__grid">
                            <div class="bps-map-info__item">
                                <span>Kategori aktif</span>
                                <strong>{{ $category->code }}</strong>
                            </div>
                            <div class="bps-map-info__item">
                                <span>Sumber peta</span>
                                <strong>OpenStreetMap</strong>
                            </div>
                            <div class="bps-map-info__item">
                                <span>Sumber data</span>
                                <strong>{{ $sourceStatus }}</strong>
                            </div>
                            <div class="bps-map-info__item">
                                <span>Pembaruan</span>
                                <strong>{{ $sourceImportedAt }}</strong>
                            </div>
                        </div>
                    </article>

                    @forelse ($mapLocations as $location)
                        <article class="bps-location-card">
                            <div class="bps-location-card__head">
                                <span class="bps-tag">{{ $location['category'] ?? 'UMUM' }}</span>
                                @if ($location['is_dummy'])
                                    <span class="bps-tag">Dummy</span>
                                @endif
                            </div>

                            <h4 class="bps-location-card__title">{{ $location['label'] }}</h4>
                            <p class="bps-location-card__meta">{{ $location['city'] }}, {{ $location['province'] }} · {{ $location['sector'] }}</p>

                            <div class="bps-location-card__grid">
                                <div class="bps-location-card__metric">
                                    <span>Tenaga kerja</span>
                                    <strong>{{ number_format($location['workforce'] ?? 0) }}</strong>
                                </div>
                                <div class="bps-location-card__metric">
                                    <span>Status</span>
                                    <strong>{{ $location['status'] }}</strong>
                                </div>
                            </div>
                        </article>
                    @empty
                        <article class="bps-empty-state">
                            Belum ada lokasi industri pada kategori ini.
                        </article>
                    @endforelse
                </aside>
            </div>
        </section>
    </main>
</div>

@once
    <script>
        if (!window.statisticsVisuals) {
            window.statisticsVisuals = function (payload) {
                return {
                    distributionChart: null,
                    mapInstance: null,
                    trendChart: null,
                    async init() {
                        await this.loadAssets();
                        this.renderTrend();
                        this.renderDistribution();
                        this.renderMap();
                    },
                    async loadAssets() {
                        if (!document.querySelector('link[data-leaflet]')) {
                            const link = document.createElement('link');
                            link.rel = 'stylesheet';
                            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                            link.setAttribute('data-leaflet', 'true');
                            document.head.appendChild(link);
                        }

                        await Promise.all([
                            this.loadScript('https://cdn.jsdelivr.net/npm/chart.js', 'Chart'),
                            this.loadScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', 'L'),
                        ]);
                    },
                    loadScript(url, globalKey) {
                        return new Promise((resolve, reject) => {
                            if (window[globalKey]) {
                                resolve(window[globalKey]);
                                return;
                            }

                            const existing = document.querySelector(`script[src="${url}"]`);

                            if (existing) {
                                existing.addEventListener('load', () => resolve(window[globalKey]), { once: true });
                                existing.addEventListener('error', reject, { once: true });
                                return;
                            }

                            const script = document.createElement('script');
                            script.src = url;
                            script.async = true;
                            script.addEventListener('load', () => resolve(window[globalKey]), { once: true });
                            script.addEventListener('error', reject, { once: true });
                            document.head.appendChild(script);
                        });
                    },
                    renderDistribution() {
                        if (!this.$refs.distributionCanvas || !window.Chart) {
                            return;
                        }

                        if (this.distributionChart) {
                            this.distributionChart.destroy();
                        }

                        this.distributionChart = new window.Chart(this.$refs.distributionCanvas, {
                            type: 'doughnut',
                            data: {
                                labels: payload.distribution.labels,
                                datasets: [{
                                    data: payload.distribution.values,
                                    backgroundColor: ['#083b82', '#1296db', '#51b8dd', '#8cc9ea', '#ef7d00'],
                                    borderColor: '#ffffff',
                                    borderWidth: 2,
                                }],
                            },
                            options: {
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'right',
                                        labels: {
                                            boxWidth: 12,
                                            color: '#334155',
                                            usePointStyle: true,
                                        },
                                    },
                                },
                                cutout: '52%',
                            },
                        });
                    },
                    renderMap() {
                        if (!this.$refs.mapCanvas || !window.L) {
                            return;
                        }

                        if (this.mapInstance) {
                            this.mapInstance.remove();
                        }

                        this.mapInstance = window.L.map(this.$refs.mapCanvas, {
                            scrollWheelZoom: false,
                        }).setView([-2.5, 118], 5);

                        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors',
                        }).addTo(this.mapInstance);

                        const fitLayers = [];
                        const categoryColors = {
                            'DSI': '#083b82',
                            'IBS': '#1296db',
                            'IMK': '#51b8dd',
                            'KEK/KI': '#ef7d00',
                        };

                        payload.map.geoJsonLayers.forEach((layer) => {
                            const featureLayer = window.L.geoJSON(layer.geojson, {
                                style: layer.style || {},
                            }).addTo(this.mapInstance);

                            if (featureLayer.getBounds && featureLayer.getBounds().isValid()) {
                                fitLayers.push(featureLayer);
                            }
                        });

                        payload.map.locations.forEach((location) => {
                            const color = categoryColors[location.category] || '#083b82';
                            const marker = window.L.circleMarker([location.latitude, location.longitude], {
                                color,
                                fillColor: color,
                                fillOpacity: 0.86,
                                radius: 8,
                                weight: 2,
                            }).addTo(this.mapInstance);

                            marker.bindPopup(`
                                <div style="min-width: 210px; font-family: Plus Jakarta Sans, sans-serif;">
                                    <strong style="display:block; font-size:14px; margin-bottom:6px;">${location.label}</strong>
                                    <span style="display:block; color:#475569;">${location.city}, ${location.province}</span>
                                    <span style="display:block; color:#64748b; margin-top:4px;">${location.sector ?? '-'}</span>
                                    <span style="display:block; margin-top:8px; font-weight:600;">Tenaga kerja: ${Number(location.workforce ?? 0).toLocaleString('id-ID')}</span>
                                </div>
                            `);

                            fitLayers.push(marker);
                        });

                        if (fitLayers.length > 0) {
                            const group = window.L.featureGroup(fitLayers);
                            this.mapInstance.fitBounds(group.getBounds().pad(0.2));
                        }
                    },
                    renderTrend() {
                        if (!this.$refs.trendCanvas || !window.Chart) {
                            return;
                        }

                        if (this.trendChart) {
                            this.trendChart.destroy();
                        }

                        this.trendChart = new window.Chart(this.$refs.trendCanvas, {
                            type: 'line',
                            data: {
                                labels: payload.trend.labels,
                                datasets: payload.trend.datasets.map((dataset, index) => {
                                    const palette = ['#083b82', '#1296db', '#51b8dd', '#ef7d00', '#7aa6d8'];
                                    const color = palette[index % palette.length];

                                    return {
                                        ...dataset,
                                        backgroundColor: color,
                                        borderColor: color,
                                        borderWidth: 3,
                                        fill: false,
                                        pointRadius: 3,
                                        pointHoverRadius: 5,
                                        tension: 0.32,
                                    };
                                }),
                            },
                            options: {
                                maintainAspectRatio: false,
                                interaction: {
                                    intersect: false,
                                    mode: 'index',
                                },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: {
                                            boxWidth: 14,
                                            color: '#334155',
                                            usePointStyle: true,
                                        },
                                    },
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            color: '#dbe7f3',
                                        },
                                        ticks: {
                                            color: '#5f7288',
                                        },
                                    },
                                    y: {
                                        grid: {
                                            color: '#dbe7f3',
                                        },
                                        ticks: {
                                            color: '#5f7288',
                                            callback(value) {
                                                return `${value}`;
                                            },
                                        },
                                    },
                                },
                            },
                        });
                    },
                };
            };
        }
    </script>
@endonce
