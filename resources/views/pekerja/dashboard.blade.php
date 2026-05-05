<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Pekerja</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.swal2-popup.swal2-toast {
  padding: 6px 14px !important;
  border-radius: 10px !important;
  width: 320px !important;
}
.swal2-title {
  font-size: 20px;
  font-weight: 600;
}
</style>

</head>

<body class="bg-[#eef1f5] font-sans">

<div class="flex h-screen">

<!-- SIDEBAR FIX -->
<aside class="w-64 flex flex-col border-r bg-[#E8E8F4]">

<div class="flex justify-center items-start pt-[22px] border-b" 
     style="height:100px; border-color:#145caa; border-bottom-width:2px;">

  <img src="{{ asset('images/kmi-logo.png') }}" 
       class="object-contain"
       style="width:135px; height:63px;">

</div>

<nav class="flex-1 text-sm mt-8">

  <a href="#" class="block bg-[#5b6ea8] text-white px-6 py-3 font-semibold">
    Dashboard
  </a>

  <a href="/pekerja/buat_permit" 
  class="block text-gray-700 px-6 py-3 hover:bg-[#dcdce6] transition">
  Buat Permit
  </a>

  <a href="#" class="block text-gray-700 px-6 py-3 hover:bg-[#dcdce6] transition">
    Riwayat Permit
  </a>

</nav>

</aside>

<!-- MAIN -->
<main class="flex-1 p-6 overflow-y-auto bg-[#eef1f5]">

<!-- HEADER -->
<div class="mb-6">
  <div class="bg-[#6b7da6] text-white px-6 py-3 rounded-full flex items-center justify-between shadow-md">

    <div class="font-semibold text-lg tracking-wide">
      PERMIT TO WORK - PT.KMI
    </div>

    <div class="flex items-center gap-4">
      <i data-lucide="bell" class="w-5 h-5"></i>

      <div class="flex items-center gap-2">
        <span class="text-lg">Pekerja</span>
        <img src="{{ asset('images/me.jpg') }}" 
        class="w-10 h-10 rounded-full border-2 border-white shadow-md object-cover">
      </div>
    </div>

  </div>
</div>

<!-- WELCOME -->
<h3 class="mb-4">
  <span class="font-bold text-2xl">Selamat Datang, </span>
  <span class="text-2xl text-gray-800">Pekerja</span>
</h3>

<!-- CARD -->
<div class="grid grid-cols-5 gap-4 mb-6 text-white text-sm">

  <div class="bg-blue-500 p-4 rounded-xl flex items-center gap-3 shadow-md">
    <div class="bg-white/30 p-2 rounded-full">
      <i data-lucide="file-text"></i>
    </div>
    <div>
      <p>Total Permit</p>
      <h1 class="text-xl font-bold">5</h1>
    </div>
  </div>

  <div class="bg-yellow-400 p-4 rounded-xl flex items-center gap-3 shadow-md">
    <div class="bg-white/30 p-2 rounded-full">
      <i data-lucide="hourglass"></i>
    </div>
    <div>
      <p>Permit Pending</p>
      <h1 class="text-xl font-bold">1</h1>
    </div>
  </div>

  <div class="bg-green-500 p-4 rounded-xl flex items-center gap-3 shadow-md">
    <div class="bg-white/30 p-2 rounded-full">
      <i data-lucide="check-circle"></i>
    </div>
    <div>
      <p>Permit Disetujui</p>
      <h1 class="text-xl font-bold">1</h1>
    </div>
  </div>

  <div class="bg-red-500 p-4 rounded-xl flex items-center gap-3 shadow-md">
    <div class="bg-white/30 p-2 rounded-full">
      <i data-lucide="x-circle"></i>
    </div>
    <div>
      <p>Permit Ditolak</p>
      <h1 class="text-xl font-bold">0</h1>
    </div>
  </div>

  <div class="bg-[#DAE3FA] p-4 rounded-xl flex items-center gap-3 shadow-md">
    <div class="bg-blue-800 p-2 rounded-full">
      <i data-lucide="check" class="text-white w-5 h-5"></i>
    </div>
    <div>
      <p class="font-bold text-blue-900">Permit Selesai</p>
      <h1 class="text-xl font-bold text-blue-900">3</h1>
    </div>
  </div>

</div>

<!-- TABLE -->
<div class="bg-white p-4 rounded-xl shadow-md mb-6">

  <div class="flex items-center gap-2 mb-3">
    <div class="relative w-64">
      
      <input 
        id="searchInput"
        type="text" 
        placeholder="Cari permit..."
        class="w-full border border-gray-300 pl-9 pr-3 py-2 rounded-lg text-sm 
               focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">

      <i data-lucide="search" 
         class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>

    </div>
  </div>

  <table class="w-full text-sm text-center">
    <thead class="bg-gray-100 text-gray-600">
      <tr>
        <th class="p-2">No. Permit</th>
        <th class="p-2">Pekerjaan</th>
        <th class="p-2">Lokasi</th>
        <th class="p-2">Tanggal Kerja</th>
        <th class="p-2">Status</th>
        <th class="p-2">Aksi</th>
      </tr>
    </thead>

    <tbody id="tableBody">

      <tr class="border-b">
        <td class="p-2">PRM-010</td>
        <td class="p-2">Pemindahan bahan kimia</td>
        <td class="p-2">Storage 3</td>
        <td class="p-2">11 April 2026</td>

        <td class="p-2">
          <div class="flex justify-center">
            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs flex items-center gap-1">
              <i data-lucide="hourglass" class="w-3 h-3"></i>
              Pending
            </span>
          </div>
        </td>

        <td class="p-2">
          <div class="flex justify-center gap-2">

            <a href="/pekerja/edit_permit/1"
            class="bg-yellow-400 text-white px-3 py-1 rounded text-xs flex items-center gap-1">
              <i data-lucide="edit" class="w-3 h-3"></i>
              Edit
            </a>

            <form id="delete-form-1">
              <button type="button"
                onclick="confirmDelete(1)"
                class="bg-red-500 text-white px-3 py-1 rounded text-xs flex items-center gap-1">
                <i data-lucide="trash" class="w-3 h-3"></i>
                Hapus
              </button>
            </form>

          </div>
        </td>
      </tr>

      <!-- ROW 2 tetap sama -->
      <tr>
        <td class="p-2">PRM-007</td>
        <td class="p-2">Perbaikan Pipa Bocor</td>
        <td class="p-2">Area Las</td>
        <td class="p-2">06 Maret 2026</td>

        <td class="p-2">
          <div class="flex justify-center">
            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs flex items-center gap-1">
              <i data-lucide="check-circle" class="w-3 h-3"></i>
              Disetujui
            </span>
          </div>
        </td>

        <td class="p-2">
          <div class="flex justify-center">
            <a href="/pekerja/detail_permit/1"
              class="bg-blue-500 text-white px-3 py-1 rounded text-xs flex items-center gap-1">
              <i data-lucide="search" class="w-3 h-3"></i>
              Lihat
            </a>
          </div>
        </td>
      </tr>

    </tbody>
  </table>

</div>

<!-- NOTIFIKASI (100% SAMA) -->
<div class="bg-white rounded-xl shadow-md overflow-hidden">

  <div class="px-4 py-3 font-semibold text-sm border-b">
    Notifikasi
  </div>

  <div class="divide-y text-sm">

    <div class="flex justify-between items-center px-4 py-3">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full border-2 border-yellow-400 flex items-center justify-center">
          <i data-lucide="hourglass" class="w-4 h-4 text-yellow-400"></i>
        </div>
        <div>
          <p class="font-semibold">PRM-010 (Pending)</p>
          <p class="text-gray-500 text-xs">
            Permit PRM-010 berhasil dibuat dan menunggu persetujuan.
          </p>
        </div>
      </div>
      <span class="text-xs text-gray-400">Sekarang</span>
    </div>

    <div class="flex justify-between items-center px-4 py-3">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full border-2 border-green-500 flex items-center justify-center">
          <i data-lucide="check" class="w-4 h-4 text-green-500"></i>
        </div>
        <div>
          <p class="font-semibold">PRM-007 (Disetujui)</p>
          <p class="text-gray-500 text-xs">
            Permit PRM-007 telah disetujui dan siap diambil di Control Building.
          </p>
        </div>
      </div>
      <span class="text-xs text-gray-400">1 Bulan yang lalu</span>
    </div>

    <div class="flex justify-between items-center px-4 py-3">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full border-2 border-gray-500 flex items-center justify-center">
          <i data-lucide="check" class="w-4 h-4 text-gray-500"></i>
        </div>
        <div>
          <p class="font-semibold">PRM-005 (Selesai)</p>
          <p class="text-gray-500 text-xs">
            Pekerjaan telah selesai dan permit ditutup.
          </p>
        </div>
      </div>
      <span class="text-xs text-gray-400">1 Bulan yang lalu</span>
    </div>

  </div>

</div>

</main>
</div>

<!-- SCRIPT -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  lucide.createIcons();

  const searchInput = document.getElementById("searchInput");
  const tableBody = document.getElementById("tableBody");
  const rows = tableBody.getElementsByTagName("tr");

  searchInput.addEventListener("keyup", function () {
    const filter = searchInput.value.toUpperCase();

    for (let i = 0; i < rows.length; i++) {
      const firstTd = rows[i].getElementsByTagName("td")[0];

      if (firstTd) {
        const textValue = firstTd.textContent || firstTd.innerText;

        if (textValue.toUpperCase().indexOf(filter) > -1) {
          rows[i].style.display = "";
        } else {
          rows[i].style.display = "none";
        }
      }
    }
  });
});

function confirmDelete(id) {
  Swal.fire({
    title: 'Yakin Hapus Permit ?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Hapus',
    cancelButtonText: 'Batal',
    reverseButtons: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#3b82f6'
  }).then((result) => {
    if (result.isConfirmed) {

      // hapus baris
      document.getElementById('delete-form-' + id)
        .closest('tr').remove();

      // 🔥 NOTIF ATAS TENGAH (SEPERTI GAMBAR)
      Swal.fire({
        toast: true,
        position: 'top',
        icon: 'success',
        title: 'Data berhasil dihapus',
        showConfirmButton: false,
        timer: 2000,
        width: '320px'
      });

    }
  });
}
</script>

</body>
</html>