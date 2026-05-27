# **Procurement System \- UI/UX Design System & Developer Guidelines**

**Konteks Dokumen:**

Dokumen ini adalah referensi *Design System* mutlak untuk pengembangan antarmuka (Frontend) pada proyek Procurement System. Filosofi desain yang diusung adalah **Modern Minimalist / Clean UI** yang dioptimalkan untuk Dashboard SaaS dan Panel Administratif.

Semua *AI Agent* atau *Developer* **WAJIB** mengikuti standarisasi kelas Tailwind CSS di bawah ini saat membuat komponen baru untuk menjaga konsistensi visual.

## **1\. Typography (Sistem Tipografi)**

Gunakan tipografi yang bersih dengan hierarki yang jelas.

* **Primary Font (Aplikasi & Dashboard):** Figtree  
  * *Implementasi:* Gunakan class font-sans antialiased pada tag \<body\>.  
  * *Aturan AI:* Gunakan font ini untuk semua teks di dalam dashboard, form, dan tabel.  
* **Secondary Font (Landing / Welcome Page):** Instrument Sans  
  * *Aturan AI:* Hanya gunakan font ini untuk elemen pemasaran atau halaman depan di luar sesi otentikasi.  
* **Hierarki Teks:**  
  * Heading utama: text-xl font-semibold leading-tight  
  * Label form: block font-medium text-sm  
  * Teks paragraf standar: text-base leading-normal  
  * Teks pendukung (*muted*): text-sm

## **2\. Color System (Palet Warna)**

Sistem warna menggunakan gaya monokromatik abu-abu yang aman untuk mata (*eye-friendly*) dengan satu warna aksen khusus untuk interaksi.

* **Backgrounds (Latar Belakang):**  
  * Base/Kanvas Utama Aplikasi: bg-gray-100 (Mencegah kelelahan mata).  
  * Surfaces/Card/Header: bg-white (Untuk mengangkat elemen dari kanvas utama).  
  * Base Landing Page (Dark Mode): dark:bg-\[\#0a0a0a\].  
  * Surface Landing Page (Dark Mode): dark:bg-\[\#161615\].  
* **Typography Colors (Warna Teks):**  
  * Teks Utama (Primary): text-gray-800 (Hindari text-black pekat).  
  * Teks Sekunder (Muted): text-gray-500 (Untuk email, caption, placeholder).  
* **Brand & Accent Colors (Warna Interaksi):**  
  * Warna Aksen Utama (Sistem): **Indigo** (indigo-500). Digunakan mutlak untuk *state* aktif dan *focus*.  
  * Warna Aksen Khusus (Landing Page): Merah-Jingga (\#f53003 atau dark:text-\[\#FF4433\]).

## **3\. UI Components & Geometry (Komponen & Bentuk)**

Elemen antarmuka harus terlihat profesional, tidak kaku, namun tidak terlalu membulat (hindari gaya *pill-shaped*).

* **Borders & Radius:**  
  * Input Fields & Buttons: rounded-md (Lengkungan medium).  
  * Card / Container: rounded-lg atau rounded-md dengan border border-gray-200 jika diperlukan.  
  * Border Input Standar: border-gray-300.  
* **Shadows (Elevasi):**  
  * Input Forms: shadow-sm (Bayangan sangat tipis untuk kedalaman dasar).  
  * Navbar & Header: shadow (Elevasi standar untuk elemen melayang).  
* **Primary Button (Tombol Utama):**  
  * *Ruleset AI:* Setiap membuat tombol *Call to Action* utama (seperti Submit, Save, Login), **HARUS** menggunakan class berikut:  
  * inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900  
* **Text Input (Kolom Input):**  
  * *Ruleset AI:* border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm

## **4\. Layout & Spacing (Tata Letak & Ruang)**

Memanfaatkan Tailwind Flexbox dan Grid untuk responsibilitas penuh.

* **Main Container:**  
  * Standar Lebar Maksimal: max-w-7xl mx-auto.  
  * Padding Aman Horizontal: px-4 sm:px-6 lg:px-8.  
  * Padding Vertikal antar seksi: py-6 atau py-12.  
* **Navbar Layout:**  
  * Tinggi statis h-16 menggunakan Flexbox flex justify-between.  
  * Navigasi utama disembunyikan di layar kecil (hidden sm:flex) dan diganti dengan ikon hamburger.  
* **Grid System:**  
  * Beri jarak antar elemen secara konsisten dengan utility gap-4 atau gap-6.

## **5\. Micro-Interactions & States (Interaksi & Animasi)**

Peralihan antar *state* tidak boleh terjadi secara instan (kedip).

* **Global Transition (Wajib untuk semua interaktif):**  
  * Class wajib: transition ease-in-out duration-150  
* **Focus State (Aksesibilitas / Form):**  
  * Semua input form saat di-klik/fokus wajib memancarkan pendar cincin Indigo:  
  * focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2  
* **Hover State:**  
  * Tombol utama: Berubah dari bg-gray-800 menjadi hover:bg-gray-700.  
  * Teks Navigasi: Berubah warna ke abu-abu lebih gelap hover:text-gray-700 atau hover:text-gray-900.

## **Instruksi Tambahan Khusus AI Developer:**

1. **Mobile First:** Selalu mulai desain struktur dari layar HP, lalu gunakan prefix sm:, md:, dan lg: untuk menyesuaikan di layar besar.  
2. **No Custom CSS:** Jangan membuat \<style\> khusus kecuali terpaksa. Selalu gunakan *utility classes* bawaan Tailwind CSS berdasarkan variabel di atas.  
3. **Semantic HTML:** Gunakan tag semantik HTML5 yang tepat (\<header\>, \<main\>, \<nav\>, \<section\>) untuk struktur dasar.