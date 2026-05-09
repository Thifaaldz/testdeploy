# TODO - Analisis Sistem Untuk Presentasi

## Judul Presentasi

Analisis Sistem Dashboard Statistik Industri BPS

## Ringkasan Sistem

Sistem ini adalah web dashboard statistik industri untuk BPS yang menampilkan data berdasarkan 4 kategori utama:

1. DSI
2. IBS
3. IMK
4. KEK/KI

Sistem memiliki dua sisi utama:

- Sisi publik untuk menampilkan dashboard statistik secara interaktif.
- Sisi admin untuk mengelola data, pengguna, impor spreadsheet, dan data peta industri.

## Masalah Yang Diselesaikan

- Data statistik masih tersebar dalam spreadsheet.
- Pembaruan data masih manual.
- Belum ada dashboard publik yang terintegrasi.
- Belum ada pengelolaan admin yang terstruktur.
- Belum ada visualisasi lokasi industri berbasis peta.

## Tujuan Sistem

- Menyediakan dashboard statistik industri yang menarik dan interaktif.
- Menampilkan statistik berdasarkan kategori DSI, IBS, IMK, dan KEK/KI.
- Memudahkan admin mengunggah data dari Excel atau link spreadsheet.
- Menyediakan modul pengelolaan lokasi industri berbasis peta.
- Mendukung pengelolaan pengguna dan hak akses admin.

## Pengguna Sistem

### Pengguna Publik

- Melihat dashboard statistik industri.
- Memilih kategori statistik.
- Melihat grafik tren data.
- Melihat distribusi data.
- Melihat progres pemasukan data.
- Melihat peta lokasi industri.

### Admin

- Login ke panel admin.
- Mengelola pengguna.
- Mengelola sumber data statistik.
- Mengunggah file Excel.
- Menambahkan link spreadsheet publik.
- Mengelola series data statistik.
- Mengelola progres survei.
- Mengelola titik lokasi industri.
- Mengunggah dan mengelola layer GeoJSON.

## Modul Utama Sistem

### 1. Dashboard Publik

- Hero dashboard dan identitas kategori aktif.
- KPI utama.
- Grafik tren statistik.
- Grafik distribusi.
- Panel progres pemasukan data.
- Peta lokasi industri berbasis OpenStreetMap.

### 2. Admin Panel

Admin dibangun menggunakan Filament untuk:

- Manajemen user.
- Manajemen sumber data.
- Manajemen data statistik.
- Manajemen progres survei.
- Manajemen lokasi industri.
- Manajemen layer peta GeoJSON.

### 3. Modul Impor Data

Mendukung:

- Upload file Excel `.xlsx`
- Link spreadsheet publik

### 4. Modul Peta Industri

- Menampilkan titik lokasi industri.
- Menampilkan layer GeoJSON.
- Mendukung visualisasi persebaran industri per kategori.

## Alur Sistem

### Alur Pengelolaan Data

1. Admin login ke panel admin.
2. Admin menambahkan sumber data.
3. Admin mengunggah file Excel atau memasukkan link spreadsheet.
4. Sistem membaca data dan memprosesnya ke tabel statistik.
5. Data statistik langsung ditampilkan di dashboard publik.

### Alur Pengelolaan Peta

1. Admin menambahkan titik lokasi industri.
2. Admin mengunggah file GeoJSON.
3. Sistem menyimpan data layer dan titik lokasi.
4. Dashboard publik menampilkan peta industri.

## Alur Sistem Lengkap

### 1. Alur Sistem Secara Umum

1. Admin masuk ke panel admin Filament.
2. Admin mengelola sumber data statistik.
3. Data diunggah melalui file Excel atau link spreadsheet.
4. Sistem membaca dan memproses data.
5. Data disimpan ke database sesuai struktur kategori, periode, series, dan titik data.
6. Dashboard publik mengambil data terbaru dari database.
7. Pengguna publik melihat statistik dalam bentuk KPI, chart, progres, dan peta.

### 2. Alur Dashboard Publik

1. Pengguna membuka halaman utama dashboard.
2. Sistem menampilkan kategori default, yaitu DSI.
3. Pengguna dapat memilih kategori lain seperti IBS, IMK, atau KEK/KI.
4. Sistem memuat data sesuai kategori yang dipilih.
5. Data ditampilkan dalam bentuk:
   KPI utama
   grafik tren
   grafik distribusi
   progres pemasukan data
   peta lokasi industri
6. Jika filter tahun atau triwulan diubah, sistem memperbarui tampilan data secara dinamis.

### 3. Alur Admin Mengelola Data Statistik

1. Admin login ke sistem.
2. Admin masuk ke menu `Data Sources`.
3. Admin memilih jenis sumber data:
   upload Excel
   link spreadsheet
4. Admin menyimpan sumber data.
5. Admin menjalankan proses import.
6. Sistem membaca file spreadsheet.
7. Sistem memetakan data ke kategori, periode, series, dan point statistik.
8. Data baru menggantikan atau memperbarui data lama.
9. Dashboard publik otomatis menggunakan data terbaru.

### 4. Alur Admin Mengelola Progres Survei

1. Admin masuk ke menu `Survey Progresses`.
2. Admin menambahkan atau memperbarui data kegiatan.
3. Sistem menyimpan data target awal, selesai dicacah, sisa target, eligible, dan sedang dicacah.
4. Dashboard publik menampilkan progres kegiatan yang dipilih.

### 5. Alur Admin Mengelola Lokasi Industri

1. Admin masuk ke menu `Industry Locations`.
2. Admin menambahkan nama industri, kategori, provinsi, kota, dan koordinat.
3. Sistem menyimpan titik lokasi ke database.
4. Data lokasi otomatis dipakai oleh peta OpenStreetMap.
5. Marker lokasi tampil di dashboard publik.

### 6. Alur Admin Mengelola GeoJSON

1. Admin masuk ke menu `Geo Json Layers`.
2. Admin mengunggah file GeoJSON atau menempelkan isi GeoJSON.
3. Sistem menyimpan layer peta ke database.
4. Layer ditampilkan di atas peta dashboard publik.

### 7. Alur Pengelolaan Pengguna

1. Super admin login ke panel admin.
2. Super admin menambahkan user baru.
3. Super admin memberikan role dan hak akses.
4. User yang sudah diberi akses dapat masuk sesuai permission yang dimiliki.

### 8. Output Akhir Sistem

Output yang dihasilkan sistem adalah:

- dashboard statistik industri yang interaktif
- data statistik yang mudah diperbarui
- panel admin untuk pengelolaan data
- visualisasi grafik yang informatif
- peta lokasi industri berbasis OpenStreetMap

## Arsitektur Sistem

### Lapisan Presentasi

- Blade
- Livewire
- Tailwind CSS
- Chart.js
- Leaflet dan OpenStreetMap

### Lapisan Logika Aplikasi

- Laravel
- Livewire Component
- Filament Resources
- Service impor spreadsheet

### Lapisan Data

- MariaDB
- Eloquent ORM

## Struktur Data Utama

- `statistic_categories`
- `statistic_periods`
- `statistic_series`
- `statistic_points`
- `data_sources`
- `survey_progresses`
- `industry_locations`
- `geo_json_layers`
- `users`

## Teknologi Yang Dipakai

| Bagian | Teknologi | Fungsi |
|---|---|---|
| Backend | Laravel 12 | Framework utama aplikasi |
| Admin Panel | Filament 3 | Panel admin dan CRUD data |
| UI Interaktif | Livewire 3 | Dashboard dinamis |
| Styling | Tailwind CSS | Desain antarmuka |
| Build Tool | Vite | Build asset frontend |
| Grafik | Chart.js | Menampilkan chart statistik |
| Peta | Leaflet + OpenStreetMap | Menampilkan lokasi industri |
| Database | MariaDB | Penyimpanan data utama |
| Auth & Permission | Spatie Permission + Filament Shield | Role dan permission admin |
| Activity Log | Filament Logger / Spatie Activity Log | Riwayat aktivitas |
| Container | Docker Compose | Menjalankan environment aplikasi |

## Kelebihan Sistem

- Tampilan statistik lebih modern dan mudah dipresentasikan.
- Data dapat dikelola tanpa mengubah kode.
- Admin panel terpisah dari tampilan publik.
- Mendukung impor Excel dan link spreadsheet.
- Mendukung visualisasi peta industri.
- Siap dikembangkan lebih lanjut.

## Keterbatasan Saat Ini

- Data awal masih dominan pada kategori DSI.
- Data peta masih dummy.
- Import spreadsheet publik perlu pengujian lebih lanjut dengan sumber nyata.

## Rencana Pengembangan

- Menambahkan data real untuk IBS, IMK, dan KEK/KI.
- Menambahkan upload GeoJSON resmi.
- Menambahkan export PDF atau laporan presentasi otomatis.
- Menambahkan filter wilayah yang lebih lengkap.
- Menambahkan notifikasi hasil impor data.

## Kesimpulan Singkat

Sistem ini adalah dashboard statistik industri BPS berbasis Laravel, Filament, dan Livewire yang mendukung tampilan publik interaktif serta pengelolaan data admin dalam satu platform. Sistem ini mempermudah penyajian statistik, pengelolaan data, dan visualisasi lokasi industri.
