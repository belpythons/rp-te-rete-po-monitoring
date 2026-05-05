<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Permit - PT KMI</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<style>
body { font-family: 'Segoe UI', sans-serif; }
</style>
</head>

<body class="bg-[#eef1f5]">

<div class="flex h-screen">

<!-- SIDEBAR -->
<aside class="w-64 flex flex-col border-r bg-[#E8E8F4]">

<div class="flex justify-center items-start pt-[22px] border-b"
style="height:100px; border-color:#145caa; border-bottom-width:2px;">
<img src="{{ asset('images/kmi-logo.png') }}" style="width:135px; height:63px;">
</div>

<nav class="flex-1 text-sm mt-8">

<a href="/pekerja/dashboard"
class="block px-6 py-3
{{ request()->is('pekerja/dashboard') || request()->is('pekerja/edit_permit*')
? 'bg-[#5b6ea8] text-white font-semibold'
: 'text-gray-700 hover:bg-[#dcdce6]' }}">
Dashboard
</a>

<a href="/pekerja/buat_permit"
class="block px-6 py-3
{{ request()->is('pekerja/buat_permit')
? 'bg-[#5b6ea8] text-white font-semibold'
: 'text-gray-700 hover:bg-[#dcdce6]' }}">
Buat Permit
</a>

<a href="/pekerja/riwayat"
class="block px-6 py-3
{{ request()->is('pekerja/riwayat*')
? 'bg-[#5b6ea8] text-white font-semibold'
: 'text-gray-700 hover:bg-[#dcdce6]' }}">
Riwayat Permit
</a>

</nav>
</aside>

<!-- MAIN -->
<main class="flex-1 p-6 overflow-y-auto">

<!-- HEADER -->
<div class="mb-6">
<div class="bg-[#6b7da6] text-white px-6 py-3 rounded-full flex justify-between items-center shadow-md">

<div class="font-semibold text-lg">
PERMIT TO WORK - PT.KMI
</div>

<div class="flex items-center gap-4">
<i data-lucide="bell"></i>

<div class="flex items-center gap-2">
<span>Pekerja</span>
<img src="{{ asset('images/me.jpg') }}"
class="w-10 h-10 rounded-full border-2 border-white object-cover">
</div>
</div>

</div>
</div>

<!-- TITLE (DI LUAR CARD) -->
<div class="mb-4 ml-1">
    <h2 class="text-[27px] font-semibold text-gray-800">Edit Permit</h2>
    <p class="text-[18px] text-gray-500 mt-1">
        Ajukan permit kerja di tempat kerja Anda
    </p>
</div>

<!-- CONTENT -->
<div class="bg-[#f3f4f6] p-6 rounded-xl border border-gray-200">

<!-- LABEL -->
<div class="flex items-center mb-4">
<div class="bg-[#5b6ea8] text-white px-4 py-1 text-sm font-">
Data Permit
</div>
<div class="w-0 h-0 border-t-[14px] border-b-[14px] border-l-[10px]
border-t-transparent border-b-transparent border-l-[#5b6ea8]"></div>
</div>

<div class="grid grid-cols-12 gap-6">

<!-- FORM -->
<div class="col-span-7">

<form method="POST" action="/pekerja/update_permit/{{ $id }}">
@csrf

<!-- JENIS -->
<div class="mb-3">
<label class="text-sm">Jenis Pekerjaan</label>
<select name="jenis" class="w-full bg-gray-200 border p-2 text-sm">
<option {{ $permit['jenis']=='Permit Confined Space'?'selected':'' }}>Permit Confined Space</option>
<option {{ $permit['jenis']=='Permit Panas (Hot Work)'?'selected':'' }}>Permit Panas (Hot Work Permit)</option>
<option {{ $permit['jenis']=='Permit Dingin (Cold Work)'?'selected':'' }}>Permit Dingin (Cold Work Permit)</option>
<option {{ $permit['jenis']=='Permit Penggalian'?'selected':'' }}>Permit Penggalian</option>
<option {{ $permit['jenis']=='Permit Listrik & Instrument'?'selected':'' }}>Listrik & Instrument</option>
<option {{ $permit['jenis']=='Permit Kendaraan & Alat-Alat Berat'?'selected':'' }}>Permit Kendaraan & Alat-Alat Berat</option>
<option {{ $permit['jenis']=='Permit Permit Confined Space'?'selected':'' }}>Permit Confined Space</option>
<option {{ $permit['jenis']=='Kompreesor Oksigen'?'selected':'' }}>Kompressor Oksigen</option>


</select>
</div>

<!-- PEKERJAAN -->
<div class="mb-3">
<label class="text-sm">Pekerjaan</label>
<input type="text" name="pekerjaan"
value="{{ $permit['pekerjaan'] }}"
class="w-full bg-gray-200 border p-2 text-sm">
</div>

<!-- DESKRIPSI -->
<div class="mb-3">
<label class="text-sm">Deskripsi Pekerjaan</label>
<textarea name="deskripsi" rows="3"
class="w-full bg-gray-200 border p-2 text-sm">{{ $permit['deskripsi'] }}</textarea>
</div>

<!-- WAKTU -->
<div class="mb-3">
<label class="text-sm">Waktu Pelaksanaan</label>
<div class="flex items-center gap-2">
<input type="date" name="tanggal" value="{{ $permit['tanggal'] }}" class="w-full bg-gray-200 border p-2 text-sm">
<input type="time" name="jam_mulai" value="{{ $permit['jam_mulai'] }}" class="w-full bg-gray-200 border p-2 text-sm">
<span class="text-sm">-</span>
<input type="time" name="jam_selesai" value="{{ $permit['jam_selesai'] }}" class="w-full bg-gray-200 border p-2 text-sm">
</div>
</div>

<!-- GEDUNG LOKASI -->
<div class="flex gap-4 mb-3">
<div class="w-1/2">
<label class="text-sm">Gedung :</label>
<select name="gedung" class="w-full bg-gray-200 border p-2 text-sm">

<option value="Adminstation"
{{ ($permit['gedung'] ?? '')=='Adminstation'?'selected':'' }}>
Adminstation
</option>

<option value="CB (Control Building)"
{{ ($permit['gedung'] ?? '')=='CB (Control Building)'?'selected':'' }}>
CB (Control Building)
</option>

<option value="Maintenance"
{{ ($permit['gedung'] ?? '')=='Maintenance'?'selected':'' }}>
Maintenance
</option>

<option value="Security"
{{ ($permit['gedung'] ?? '')=='Security'?'selected':'' }}>
Security
</option>

<option value="Klorin Unit"
{{ ($permit['gedung'] ?? '')=='Klorin Unit'?'selected':'' }}>
Klorin Unit
</option>

<option value="Jetty"
{{ ($permit['gedung'] ?? '')=='Jetty'?'selected':'' }}>
Jetty
</option>

</select>
</div>

<div class="w-1/2">
<label class="text-sm">Lokasi :</label>
<input type="text" name="lokasi"
value="{{ $permit['lokasi'] }}"
class="w-full bg-gray-200 border p-2 text-sm">
</div>
</div>

<!-- RISIKO -->
<div class="mb-3">
<label class="text-sm">Tingkat Risiko</label>
<select name="risiko" class="w-full bg-gray-200 border p-2 text-sm">
<option {{ $permit['risiko']=='Risiko Tinggi'?'selected':'' }}>Risiko Tinggi</option>
<option {{ $permit['risiko']=='Risiko Sedang'?'selected':'' }}>Risiko Sedang</option>
<option {{ $permit['risiko']=='Risiko Sedang'?'selected':'' }}>Risiko Rendah</option>
</select>
</div>

<!-- SAFETY + CATATAN -->
<div class="grid grid-cols-2 gap-4 mt-3">

<!-- LEFT -->
<div>
<label class="text-sm">Kelengkapan Safety</label>

<div class="mt-2 text-sm space-y-1">
<label><input type="checkbox" name="safety[]" value="Helm" {{ in_array('Helm',$permit['safety'])?'checked':'' }}> Helm</label><br>
<label><input type="checkbox" name="safety[]" value="Sarung Tangan" {{ in_array('Sarung Tangan',$permit['safety'])?'checked':'' }}> Sarung Tangan</label><br>
<label><input type="checkbox" name="safety[]" value="Sepatu Safety" {{ in_array('Sepatu Safety',$permit['safety'])?'checked':'' }}> Sepatu Safety</label><br>
<label><input type="checkbox" name="safety[]" value="APAR"> APAR</label><br>

<label class="flex items-center gap-2">
<input type="checkbox" checked>
Lainnya :
<input type="text" name="lainnya"
value="{{ $permit['lainnya'] }}"
class="bg-gray-200 border p-1 text-sm">
</label>
</div>
</div>

<!-- RIGHT -->
<div>
<textarea
placeholder="Contoh : APAR tidak tersedia"
class="w-full h-full bg-gray-200 border p-2 text-sm"></textarea>
</div>

</div>

<!-- BUTTON -->
<div class="mt-6 flex justify-end gap-2">
<a href="/pekerja/dashboard" class="bg-gray-300 px-4 py-2 text-sm rounded">
Batal
</a>

<button class="bg-[#5b6ea8] text-white px-4 py-2 text-sm rounded">
Update Permit
</button>
</div>

</form>

</div>

<!-- IMAGE -->
<div class="col-span-5">
<img class="rounded-lg"
src="https://cdn.pixabay.com/photo/2023/03/09/12/56/engineer-7842642_1280.jpg">
</div>

</div>
</div>

</main>
</div>

<script>
lucide.createIcons();
</script>

</body>
</html>