# Analisis Sistem Dashboard Statistik Industri BPS

## 1. Gambaran Umum Sistem

Sistem ini adalah web dashboard statistik industri untuk BPS yang menampilkan data berdasarkan 4 kategori utama:

1. DSI
2. IBS
3. IMK
4. KEK/KI

Sistem dibangun agar memiliki dua sisi utama:

- Sisi publik untuk menampilkan dashboard statistik secara interaktif.
- Sisi admin untuk mengelola data, pengguna, impor spreadsheet, dan data peta industri.

Tujuan utama sistem ini adalah membuat proses penyajian data statistik industri menjadi lebih cepat, lebih visual, dan lebih mudah diperbarui tanpa harus mengubah kode setiap kali ada data baru.

---

## 2. Latar Belakang Masalah

Sebelum sistem ini dibuat, data statistik industri umumnya masih tersebar dalam file spreadsheet dan belum terintegrasi dalam satu dashboard web yang rapi. Kondisi ini menimbulkan beberapa kendala:

- Data sulit dipresentasikan secara cepat.
- Pembaruan data masih manual.
- Belum ada pemisahan yang jelas antara tampilan publik dan pengelolaan admin.
- Data lokasi industri belum divisualisasikan dalam bentuk peta.
- Belum ada sistem manajemen pengguna dan kontrol akses admin yang terstruktur.

---

## 3. Tujuan Sistem

Sistem ini dirancang untuk:

- Menyediakan dashboard statistik industri yang menarik dan interaktif.
- Menampilkan statistik berdasarkan kategori DSI, IBS, IMK, dan KEK/KI.
- Memudahkan admin mengunggah data dari Excel atau link spreadsheet.
- Menyediakan modul pengelolaan lokasi industri berbasis peta.
- Mendukung pengelolaan pengguna dan hak akses admin.

---

## 4. Pengguna Sistem

### 4.1 Pengguna Publik

Pengguna publik dapat:

- Melihat dashboard statistik industri.
- Memilih kategori statistik.
- Melihat grafik tren data.
- Melihat distribusi data.
- Melihat progres pemasukan data.
- Melihat peta lokasi industri.

### 4.2 Admin

Admin dapat:

- Login ke panel admin.
- Mengelola pengguna.
- Mengelola sumber data statistik.
- Mengunggah file Excel.
- Menambahkan link spreadsheet publik.
- Mengelola series data statistik.
- Mengelola progres survei.
- Mengelola titik lokasi industri.
- Mengunggah dan mengelola layer GeoJSON.

---

## 5. Modul Utama Sistem

### 5.1 Dashboard Publik

Modul ini menampilkan:

- Hero dashboard dan identitas kategori aktif.
- KPI utama.
- Grafik tren statistik.
- Grafik distribusi.
- Panel progres pemasukan data.
- Peta lokasi industri berbasis OpenStreetMap.

### 5.2 Admin Panel

Modul admin dibangun menggunakan Filament dan berfungsi sebagai pusat pengelolaan sistem, meliputi:

- Manajemen user.
- Manajemen sumber data.
- Manajemen data statistik.
- Manajemen progres survei.
- Manajemen lokasi industri.
- Manajemen layer peta GeoJSON.

### 5.3 Modul Impor Data

Modul ini mendukung dua jenis input:

- Upload file Excel `.xlsx`
- Link spreadsheet publik

Setelah data diimpor, sistem akan memetakan isi spreadsheet ke struktur data statistik internal.

### 5.4 Modul Peta Industri

Modul peta digunakan untuk:

- Menampilkan titik lokasi industri.
- Menampilkan layer GeoJSON.
- Mendukung visualisasi persebaran industri per kategori.

---

## 6. Analisis Proses Bisnis Sistem

### 6.1 Alur Pengelolaan Data

1. Admin login ke panel admin.
2. Admin menambahkan sumber data.
3. Admin mengunggah file Excel atau memasukkan link spreadsheet.
4. Sistem membaca data dan memprosesnya ke tabel statistik.
5. Data statistik langsung ditampilkan di dashboard publik.

### 6.2 Alur Pengelolaan Peta

1. Admin menambahkan titik lokasi industri.
2. Admin dapat mengunggah file GeoJSON.
3. Sistem menyimpan data layer dan titik lokasi.
4. Dashboard publik menampilkan peta industri dari data tersebut.

### 6.3 Alur Akses Pengguna

1. Pengguna publik langsung mengakses halaman dashboard.
2. Admin mengakses halaman `/admin`.
3. Hak akses admin dikontrol oleh sistem role dan permission.

---

## 7. Arsitektur Sistem

Sistem menggunakan arsitektur web modern berbasis Laravel dengan pembagian berikut:

### 7.1 Lapisan Presentasi

- Blade
- Livewire
- Tailwind CSS
- Chart.js
- Leaflet / OpenStreetMap

Lapisan ini bertanggung jawab untuk menampilkan dashboard publik dan antarmuka admin.

### 7.2 Lapisan Logika Aplikasi

- Laravel Controllers dan Services
- Livewire Component
- Filament Resources
- Import Service Spreadsheet

Lapisan ini menangani proses bisnis seperti impor data, penyusunan KPI, pengelolaan series statistik, dan pengolahan data peta.

### 7.3 Lapisan Data

- MariaDB
- Eloquent ORM

Lapisan ini digunakan untuk menyimpan kategori statistik, series data, titik data, sumber data, progres survei, lokasi industri, dan layer GeoJSON.

---

## 8. Struktur Data Utama

Entity utama dalam sistem ini meliputi:

- `statistic_categories`
  Menyimpan kategori DSI, IBS, IMK, dan KEK/KI.

- `statistic_periods`
  Menyimpan periode statistik seperti Q1 2024, Q2 2024, dan seterusnya.

- `statistic_series`
  Menyimpan jenis indikator statistik, misalnya indeks IBS, pertumbuhan IMK, share output KEK/KI.

- `statistic_points`
  Menyimpan nilai statistik untuk setiap series dan periode.

- `data_sources`
  Menyimpan sumber data dari upload Excel atau spreadsheet link.

- `survey_progresses`
  Menyimpan progres pemasukan data lapangan.

- `industry_locations`
  Menyimpan titik lokasi industri.

- `geo_json_layers`
  Menyimpan layer wilayah berbentuk GeoJSON.

- `users`
  Menyimpan pengguna admin.

---

## 9. Teknologi yang Digunakan

Berikut teknologi utama yang dipakai dalam sistem:

| Bagian | Teknologi | Fungsi |
|---|---|---|
| Backend | Laravel 12 | Framework utama aplikasi |
| Admin Panel | Filament 3 | Panel admin dan CRUD data |
| Interaktif UI | Livewire 3 | Dashboard dinamis tanpa full reload |
| Styling | Tailwind CSS | Desain antarmuka |
| Build Tool | Vite | Build asset frontend |
| Grafik | Chart.js | Menampilkan chart statistik |
| Peta | Leaflet + OpenStreetMap | Menampilkan lokasi industri dan layer peta |
| Database | MariaDB | Penyimpanan data utama |
| ORM | Eloquent | Akses database berbasis model |
| Auth & Permission | Spatie Permission + Filament Shield | Role dan permission admin |
| Activity Log | Filament Logger / Spatie Activity Log | Mencatat aktivitas admin |
| Container | Docker Compose | Menjalankan environment aplikasi |

### Teknologi pendukung lain

- PHP 8.3
- JavaScript
- Blade Template
- ZipArchive + SimpleXML untuk membaca file XLSX

---

## 10. Kelebihan Sistem

Beberapa keunggulan sistem ini adalah:

- Tampilan statistik lebih modern dan mudah dipresentasikan.
- Data dapat dikelola tanpa mengubah kode program.
- Admin panel sudah terpisah dengan jelas dari tampilan publik.
- Mendukung impor Excel dan link spreadsheet.
- Mendukung visualisasi lokasi industri di peta.
- Siap dikembangkan untuk data BPS lain di masa depan.

---

## 11. Keterbatasan Sistem Saat Ini

Saat ini sistem masih memiliki beberapa batasan:

- Data awal masih dominan pada kategori DSI.
- Data peta masih menggunakan dummy data awal.
- Import spreadsheet publik sudah disiapkan, tetapi perlu pengujian lebih lanjut dengan sumber eksternal nyata.
- Desain dan struktur analitik masih bisa diperluas untuk laporan yang lebih detail.

---

## 12. Rencana Pengembangan

Pengembangan selanjutnya yang bisa dilakukan:

- Menambahkan data real untuk IBS, IMK, dan KEK/KI.
- Menambahkan upload GeoJSON resmi dari wilayah industri.
- Menambahkan export PDF atau laporan presentasi otomatis.
- Menambahkan filter wilayah, tahun, dan kategori yang lebih lengkap.
- Menambahkan notifikasi ketika data baru berhasil diimpor.
- Menambahkan audit trail yang lebih detail untuk setiap perubahan data.

---

## 13. Kesimpulan

Sistem ini merupakan solusi digital untuk pengelolaan dan penyajian statistik industri BPS dalam bentuk dashboard web modern. Dengan kombinasi Laravel, Filament, Livewire, Chart.js, dan OpenStreetMap, sistem mampu:

- Menyajikan data statistik industri secara visual.
- Mendukung pengelolaan data secara mandiri oleh admin.
- Menampilkan analisis statistik dan peta lokasi industri dalam satu platform.

Secara keseluruhan, sistem ini layak digunakan sebagai fondasi dashboard statistik industri BPS yang dapat terus dikembangkan sesuai kebutuhan organisasi.

---

## 14. Ringkasan Singkat Untuk Presentasi Lisan

Jika ingin disampaikan secara singkat saat presentasi:

> Sistem ini adalah dashboard statistik industri BPS berbasis web yang dibangun menggunakan Laravel, Filament, dan Livewire. Sistem memiliki dua sisi utama, yaitu dashboard publik untuk menampilkan statistik industri dan panel admin untuk mengelola data, pengguna, impor spreadsheet, serta data peta industri. Data ditampilkan dalam bentuk KPI, chart, progres survei, dan peta OpenStreetMap sehingga lebih mudah dianalisis dan dipresentasikan.
