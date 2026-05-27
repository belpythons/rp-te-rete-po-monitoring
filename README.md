# Procurement System — PT. Kaltim Methanol Industri

Sistem manajemen pengadaan barang terpadu untuk mengelola seluruh siklus hidup proses pengadaan mulai dari **Request Purchasing (RP)** → **Technical Evaluation (TE)** → **Re-Technical Evaluation (RE-TE)** → **Purchase Order (PO)**.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 12 (PHP 8.5) |
| **Frontend (Legacy)** | Blade Templates + Bootstrap 5 + Alpine.js |
| **Frontend (Report Module)** | Vue 3 + Inertia.js (Hybrid SPA) |
| **Styling** | Tailwind CSS 3 — Modern Minimalist / Clean UI (aligned with legacy app colors) |
| **Database** | SQLite (development) |
| **Queue** | Laravel Queue (database driver) |
| **WebSocket** | Laravel Reverb |
| **Import/Export** | Maatwebsite Excel 4.x |
| **PDF** | Barryvdh DomPDF |

## Arsitektur & Integrasi Hibrida

Aplikasi ini menggunakan **arsitektur hibrida**: modul pengadaan (RP, TE, RE-TE, PO) dibangun menggunakan Blade, Bootstrap 5, dan Alpine.js, sedangkan **modul Report** dibangun sebagai Single Page Application (SPA) menggunakan Vue 3 + Inertia.js.

Kedua ekosistem ini diselaraskan secara visual agar transisi antarhalaman terasa mulus:
1. **Sidebar yang Identik**: `ReportLayout.vue` menduplikasi struktur dan styling dari sidebar Blade dengan background `#2f3f52` dan active state `#456ea4`.
2. **Background Image yang Sama**: Seluruh aplikasi menggunakan gambar latar belakang `kmi2.jpg` yang diatur tetap (`bg-fixed`), menutup seluruh halaman (`bg-cover`), dan terletak di tengah (`bg-center`).
3. **Card Box Semi-Transparan**: Halaman Laporan menggunakan card pembungkus semi-transparan `bg-white/95 backdrop-blur-[6px]` dengan sudut membulat `rounded-[20px]` untuk menyelaraskan dengan panel konten pada halaman Blade.

```
resources/js/app.js          → Halaman Blade
resources/js/app-inertia.js  → Halaman Vue (Report Module)
```

## Fitur Laporan

### 1. Import Excel (Background Queue)
- Upload file `.xlsx` / `.csv` via drag-and-drop interface.
- Proses import berjalan di background menggunakan **Laravel Queue**.
- Pembacaan file di-chunk per **1000 baris** untuk efisiensi memori.
- Logika **upsert** berdasarkan `kode_pengadaan` (mencegah duplikasi).
- Validasi per-baris dengan pesan error bahasa Indonesia.

### 2. Real-time Progress Tracking (WebSocket)
- Menggunakan **Laravel Reverb** sebagai WebSocket server.
- **Laravel Echo** di frontend mendengarkan private channel `import.{id}`.
- Progress bar bergerak real-time setiap chunk selesai diproses.
- Notifikasi instan saat import selesai (sukses/gagal).

### 3. Error Log Otomatis
- Baris yang gagal validasi dikumpulkan via file JSONL.
- Setelah import selesai, otomatis di-generate file **Excel Error Log**.
- Setiap baris gagal dilengkapi kolom **"Alasan Gagal / Error"**.
- File dapat di-download langsung dari UI.

### 4. Export Laporan
- **Export Excel** — Data master lengkap dengan header berwarna dan auto-size.
- **Export PDF** — Format kertas **Folio/F4 Landscape** (`setPaper([0, 0, 612, 936], 'landscape')`) dengan tabel responsif.
- **Download Template** — Template import siap pakai dengan contoh dan panduan format.

### 5. Auto-Status Computation
Status procurement dihitung otomatis berdasarkan prioritas pengisian tanggal:
```
PO (tanggal_po terisi) > RE-TE (tanggal_rete terisi) > TE (tanggal_te terisi) > RP (default)
```

## Prasyarat

- PHP >= 8.2 dengan ekstensi: `zip`, `xml`, `mbstring`
- Composer 2.x
- Node.js >= 18 + npm
- SQLite 3

## Instalasi

```bash
# 1. Clone repository
git clone <repository-url>
cd procurement-system

# 2. Install dependencies
composer install
npm install

# 3. Konfigurasi environment
cp .env.example .env
php artisan key:generate

# 4. Migrasi dan seed database
php artisan migrate:fresh --seed

# 5. Build assets
npm run build
```

## Development

Untuk menjalankan aplikasi di mode development, **4 service harus berjalan secara paralel**:

```bash
# Terminal 1 — Web Server
php artisan serve

# Terminal 2 — Vite Dev Server (HMR)
npm run dev

# Terminal 3 — Queue Worker (untuk proses import background)
php artisan queue:listen

# Terminal 4 — WebSocket Server (untuk real-time progress)
php artisan reverb:start
```

Akses aplikasi di: `http://localhost:8000`

### Akun Default
| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@outlook.com` | `adminkmi123` |

## Struktur Modul Report

```
app/
├── Events/
│   ├── ImportProgressEvent.php     # Broadcast progress per-chunk
│   └── ImportCompletedEvent.php    # Broadcast hasil akhir import
├── Exports/
│   ├── ProcurementExport.php       # Export data master ke Excel
│   ├── ProcurementTemplateExport.php # Template import kosong
│   └── FailedRowsExport.php        # Error log baris gagal
├── Http/Controllers/
│   └── ReportController.php        # 6 endpoints (index, import, template, excel, pdf, errorlog)
├── Imports/
│   └── ProcurementImport.php       # Queued chunk import + upsert + validation
└── Models/
    ├── Procurement.php             # Auto-status mutator via boot event
    └── ImportLog.php               # Tracking progress import

resources/js/
├── app-inertia.js                  # Vue 3 + Inertia + Echo/Reverb setup
├── Layouts/
│   └── ReportLayout.vue            # Hibrida layout sidebar terintegrasi
└── Pages/Report/
    └── Index.vue                   # Dashboard laporan (import/export/tabel/history)
```

## API Routes — Report Module

| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `/report` | Halaman dashboard report (Inertia) |
| `POST` | `/report/import` | Upload & dispatch import job |
| `GET` | `/report/template` | Download template Excel |
| `GET` | `/report/export/excel` | Export data master ke Excel |
| `GET` | `/report/export/pdf` | Export data master ke PDF (Folio Landscape) |
| `GET` | `/report/error-log/{id}` | Download error log import |

## Design System

UI mengikuti filosofi **Modern Minimalist / Clean UI** sesuai `Design System Guide.md`:
- Font: **Figtree** (sans-serif)
- Palette: Monochromatic gray dengan aksen **Indigo** untuk focus state.
- Cards: `bg-white/95 backdrop-blur-[6px] rounded-[20px] shadow-[0_5px_15px_rgba(0,0,0,0.10)]` pada kanvas `bg-cover bg-center bg-fixed bg-no-repeat` menggunakan `images/kmi2.jpg`.
- Buttons: `bg-gray-800 hover:bg-gray-700` dengan `focus:ring-indigo-500`.
- Transitions: `transition ease-in-out duration-150` pada semua elemen interaktif.

## License

Proprietary — PT. Kaltim Methanol Industri. All rights reserved.
