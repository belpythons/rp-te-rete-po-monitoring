<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Permit</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>

</head>

<body class="bg-[#eef1f5] font-sans">

<div class="flex h-screen">

<!-- SIDEBAR -->
<aside class="w-64 flex flex-col border-r bg-[#E8E8F4]">

<div class="flex justify-center items-start pt-[22px] border-b" 
     style="height:100px; border-color:#145caa; border-bottom-width:2px;">
  <img src="{{ asset('images/kmi-logo.png') }}" 
       class="object-contain"
       style="width:135px; height:63px;">
</div>

<nav class="flex-1 text-sm mt-8">

  <a href="/pekerja/dashboard" 
     class="block bg-[#5b6ea8] text-white px-6 py-3 font-semibold">
    Dashboard
  </a>

  <a href="#" class="block text-gray-700 px-6 py-3 hover:bg-[#dcdce6]">
    Buat Permit
  </a>

  <a href="#" class="block text-gray-700 px-6 py-3 hover:bg-[#dcdce6]">
    Riwayat Permit
  </a>

</nav>

</aside>

<!-- MAIN -->
<main class="flex-1 p-6 overflow-y-auto bg-[#eef1f5]">

<!-- HEADER -->
<div class="mb-6">
  <div class="bg-[#6b7da6] text-white px-6 py-3 rounded-full flex justify-between items-center shadow-md">

    <div class="font-semibold text-lg tracking-wide">
      PERMIT TO WORK - PT.KMI
    </div>

    <div class="flex items-center gap-4">
      <i data-lucide="bell" class="w-5 h-5"></i>

      <div class="flex items-center gap-2">
        <span class="text-lg">Pekerja</span>
        <img src="{{ asset('images/me.jpg') }}" 
             class="w-10 h-10 rounded-full border-2 border-white object-cover">
      </div>
    </div>

  </div>
</div>

<!-- TITLE -->
<div class="mb-4 flex justify-between items-center bg-white p-4 rounded-md shadow-sm border">
  <div>
    <h2 class="text-xl font-bold text-gray-800">
      Detail Permit
    </h2>
    <p class="text-sm text-gray-600 mt-1">
      Nomor Permit : <span class="font-medium">{{ $permit['nomor'] }}</span>
    </p>
  </div>

  <span class="bg-green-600 text-white text-xs px-3 py-1 rounded">
    {{ $permit['status'] }}
  </span>
</div>

<!-- CONTAINER -->
<div class="bg-[#eeeeee] p-5 rounded-md space-y-5">

<!-- INFORMASI PERMIT -->
<div class="bg-white rounded-md border overflow-hidden">
  <div class="bg-gray-100 px-3 py-2 font-semibold flex items-center gap-2">
    <i data-lucide="folder" class="w-4 h-4 text-blue-600"></i>
    Informasi Permit
  </div>

  <div class="grid grid-cols-[200px_1fr] text-sm">
    <div class="p-2 border-t text-gray-600">Nomor Permit</div>
    <div class="p-2 border-t font-medium">{{ $permit['nomor'] }}</div>

    <div class="p-2 border-t text-gray-600">Jenis Pekerjaan</div>
    <div class="p-2 border-t font-medium">{{ $permit['jenis'] }}</div>

    <div class="p-2 border-t text-gray-600">Tanggal Pelaksanaan</div>
    <div class="p-2 border-t font-medium">{{ $permit['tgl_kerja'] }}</div>

    <div class="p-2 border-t text-gray-600">Waktu Pelaksanaan</div>
    <div class="p-2 border-t font-medium">{{ $permit['waktu'] ?? '09.00 - 12.00' }}</div>
  </div>
</div>

<!-- INFORMASI PEKERJA -->
<div class="bg-white rounded-md border overflow-hidden">
  <div class="bg-gray-100 px-3 py-2 font-semibold flex items-center gap-2">
    <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
    Informasi Pekerja
  </div>

  <div class="grid grid-cols-[200px_1fr] text-sm">
    <div class="p-2 border-t text-gray-600">Nama Pekerja</div>
    <div class="p-2 border-t font-medium">{{ $permit['nama'] }}</div>

    <div class="p-2 border-t text-gray-600">Departemen</div>
    <div class="p-2 border-t font-medium">{{ $permit['departemen'] }}</div>

    <div class="p-2 border-t text-gray-600">Section</div>
    <div class="p-2 border-t font-medium">{{ $permit['section'] }}</div>

    <div class="p-2 border-t text-gray-600">Supervisor</div>
    <div class="p-2 border-t font-medium">{{ $permit['supervisor'] }}</div>
  </div>
</div>

<!-- LOKASI -->
<div class="bg-white rounded-md border">
  <div class="bg-gray-100 px-3 py-2 font-semibold flex items-center gap-2">
    <i data-lucide="map-pin" class="w-4 h-4 text-blue-600"></i>
    Lokasi Pekerjaan
  </div>

  <div class="grid grid-cols-2 text-sm">
    <div class="p-2 border-t">Gedung</div>
    <div class="p-2 border-t">: {{ $permit['gedung'] ?? 'Maintenance' }}</div>

    <div class="p-2 border-t">Lokasi</div>
    <div class="p-2 border-t">: {{ $permit['lokasi'] }}</div>
  </div>
</div>

<!-- DESKRIPSI -->
<div class="bg-white rounded-md border">
  <div class="bg-gray-100 px-3 py-2 font-semibold flex items-center gap-2">
    <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
    Deskripsi Pekerjaan
  </div>

  <div class="p-3 text-sm">
    {{ $permit['deskripsi'] }}
  </div>
</div>

<!-- RISIKO -->
<div class="bg-white rounded-md border">
  <div class="bg-gray-100 px-3 py-2 font-semibold flex items-center gap-2">
    <span class="w-3 h-3 bg-red-500 rounded-full inline-block"></span>
    Tingkat Risiko
  </div>

  <div class="p-3 text-sm flex justify-between items-center">
    <div>
      @foreach($permit['apd'] as $item)
        <div class="flex items-center gap-2">
          <i data-lucide="check-square" class="w-4 h-4 text-green-600"></i>
          {{ $item }}
        </div>
      @endforeach
    </div>

    <span class="bg-green-600 text-white px-3 py-1 text-xs rounded">
      Risiko Rendah
    </span>
  </div>
</div>

<!-- CATATAN SUPERVISOR -->
<div class="bg-yellow-100 border border-yellow-400 rounded-md p-3 text-sm">
  <div class="font-semibold flex items-center gap-2 text-black">
    <i data-lucide="edit" class="w-4 h-4 text-red-500"></i>
    Catatan Supervisor
  </div>
  <p class="mt-1 text-yellow-900">
    {{ $permit['catatan_spv'] ?? 'Pekerjaan dapat dilaksanakan sesuai prosedur yang ditentukan.' }}
  </p>
</div>

<!-- CATATAN SAFETY -->
<div class="bg-red-100 border border-red-400 rounded-md p-3 text-sm">
  <div class="font-semibold flex items-center gap-2 text-black">
    <i data-lucide="edit" class="w-4 h-4 text-red-500"></i>
    Catatan Safety Officer
  </div>
  <p class="mt-1 text-red-900">
    {{ $permit['catatan_safety'] ?? 'Pastikan area aman dari percikan api selama bekerja.' }}
  </p>
</div>

</div>

</main>
</div>

<script>
lucide.createIcons();
</script>

</body>
</html>