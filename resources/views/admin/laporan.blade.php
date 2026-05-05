<!DOCTYPE html>
<html>
<head>

<title>Laporan - PT KMI</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

body{
font-family:'Segoe UI', sans-serif;
margin:0;
background: url('/images/kmi2.jpg') no-repeat center center fixed;
background-size: cover;
}

/* SIDEBAR */
.sidebar{
width:240px;
height:100vh;
background:#2f3f52;
position:fixed;
color:white;
display:flex;
flex-direction:column;
z-index:1;
}

.sidebar-header{
padding:20px;
text-align:center;
border-bottom:1px solid rgba(255,255,255,0.1);
}

.sidebar-header img{
width:140px;
}

.sidebar a{
display:block;
padding:14px 22px;
color:#dce4ec;
text-decoration:none;
font-size:15px;
}

.sidebar a:hover{
background:#3e5670;
}

.sidebar .active{
background:#456ea4;
color:white;
}

.logout{
margin-top:auto;
padding:20px;
text-align:center;
}

/* CONTENT */
.content{
margin-left:240px;
padding:25px;
position:relative;
z-index:2;
}

/* TOPBAR */
.topbar{
background:rgba(22, 61, 87, 0.9);
color:white;
padding:12px 25px;
border-radius:30px;
display:flex;
justify-content:space-between;
align-items:center;
}

.user-info{
display:flex;
align-items:center;
gap:12px;
}

.user-avatar{
width:35px;
height:35px;
border-radius:50%;
object-fit:cover;
}

/* CARD */
.card-box{
background:white;
border-radius:18px;
padding:18px;
margin-top:20px;
box-shadow:0 3px 10px rgba(0,0,0,0.08);
}

/* FILTER BAR */
.filter-bar{
background:#eef2f6;
padding:10px 15px;
border-radius:10px;
display:flex;
justify-content:space-between;
align-items:center;
flex-wrap:wrap;
gap:10px;
margin-bottom:10px;
}

.filter-group{
display:flex;
align-items:center;
gap:10px;
}

/* SUMMARY */
.summary-bar{
background:#eef2f6;
padding:8px 15px;
border-radius:10px;
font-size:13px;
margin-bottom:10px;
display:flex;
gap:15px;
flex-wrap:wrap;
}

.summary-item{
display:flex;
align-items:center;
gap:5px;
}

.circle{
width:8px;
height:8px;
border-radius:50%;
}

/* TABLE */
.table{
background:white;
border-radius:10px;
overflow:hidden;
}

.table thead{
background:#e9edf2;
font-size:13px;
}

.table td{
font-size:13px;
vertical-align:middle;
text-align:center;
}

/* STATUS */
.status{
padding:4px 10px;
border-radius:12px;
font-size:12px;
color:white;
}

.pending{background:#f39c12;}
.disetujui{background:#27ae60;}
.ditolak{background:#e74c3c;}
.selesai{background:#5dade2;}

.rendah{background:#2ecc71;}
.sedang{background:#f1c40f; color:black;}
.tinggi{background:#e74c3c;}

/* BUTTON */
.btn-detail{
background:#3e6e8f;
color:white;
border:none;
border-radius:6px;
padding:4px 10px;
font-size:12px;
text-decoration:none;
display:inline-block;
}

.btn-detail:hover{
background:#2c5364;
color:white;
}

.summary-item{
cursor:pointer;
}

.summary-item:hover{
opacity:0.7;
}

.summary-item.active {
    background:#d6e4f0;
}

</style>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

<div class="sidebar-header">
<img src="/images/kmi-logo.png">
</div>

<a href="/admin/dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
<a href="/admin/users"><i class="bi bi-people"></i> Manajemen User</a>
<a href="{{ route('admin.monitoring') }}"><i class="bi bi-clipboard-data"></i> Monitoring Permit</a>
<a href="{{ url('/admin/riwayat_permit') }}"><i class="bi bi-clock-history"></i> Riwayat Permit</a>
<a class="active" href="{{ url('/admin/laporan') }}"><i class="bi bi-bar-chart"></i> Laporan</a>

<div class="logout">
<form method="POST" action="/logout">
@csrf
<button type="submit" class="btn btn-danger w-100 mb-2">Logout</button>
</form>
<span>Admin</span>
</div>

</div>

<!-- CONTENT -->
<div class="content">

<div class="topbar">
<b>PERMIT TO WORK - PT.KMI</b>

<div class="user-info">
<i class="bi bi-bell"></i>
<span>Admin</span>
<img src="/images/me.jpg" class="user-avatar">
</div>
</div>

<h4 class="mt-4"><b>Laporan Permit</b></h4>
<p>Lihat dan unduh laporan permit kerja dalam sistem Permit to Work</p>

<div class="card-box">

<!-- FILTER -->
<div class="filter-bar">

<div class="filter-group">
<label>Filter Tanggal :</label>
<input type="date" id="startDate" class="form-control" style="width:160px">

<label>Filter Status :</label>
<select id="filterStatus" class="form-control" style="width:150px">
<option value="semua">Semua Status</option>
<option value="pending">Pending</option>
<option value="disetujui">Disetujui</option>
<option value="ditolak">Ditolak</option>
<option value="selesai">Selesai</option>
</select>
</div>

<div>
<button class="btn btn-danger btn-sm">Ekspor PDF</button>
<button class="btn btn-success btn-sm">Ekspor Excel</button>
</div>

</div>

<!-- SUMMARY -->
<div class="summary-bar">

<div class="summary-item" data-status="semua">
<div class="circle" style="background:#34495e"></div> Semua : 10
</div>

<div class="summary-item" data-status="pending">
<div class="circle" style="background:#f39c12"></div> Pending : 2
</div>

<div class="summary-item" data-status="disetujui">
<div class="circle" style="background:#27ae60"></div> Disetujui : 1
</div>

<div class="summary-item" data-status="ditolak">
<div class="circle" style="background:#e74c3c"></div> Ditolak : 2
</div>

<div class="summary-item" data-status="selesai">
<div class="circle" style="background:#5dade2"></div> Selesai : 5
</div>

</div>

<!-- TABLE -->
<div class="table-responsive">
<table class="table table-bordered">

<thead>
<tr>
<th>No Permit</th>
<th>Nama</th>
<th>Jenis</th>
<th>Pekerjaan</th>
<th>Lokasi</th>
<th>Tanggal</th>
<th>Status</th>
<th>Risiko</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

<tr>
<td>PRM-010</td>
<td>Mira Agustiansyah</td>
<td>Confined</td>
<td>Pembersihan bahan kimia</td>
<td>UNIT 1200</td>
<td class="tgl">11 April 2026</td>
<td><span class="status pending">Pending</span></td>
<td><span class="status tinggi">Risiko Tinggi</span></td>
<td>
<a href="/admin/laporan/detail/PRM-010" class="btn-detail">Detail</a>
</td>
</tr>

<tr>
<td>PRM-009</td>
<td>Dinda Cahya</td>
<td>Confined</td>
<td>Pemindahan bahan kimia</td>
<td>UNIT 1200</td>
<td class="tgl">03 April 2026</td>
<td><span class="status ditolak">Ditolak</span></td>
<td><span class="status tinggi">Risiko Tinggi</span></td>
<td>
<a href="/admin/laporan/detail/PRM-009" class="btn-detail">Detail</a>
</td>
</tr>

<tr>
<td>PRM-008</td>
<td>Yoga Prasetya</td>
<td>Hot Work</td>
<td>Pengelasan</td>
<td>UNIT 1500</td>
<td class="tgl">19 Maret 2026</td>
<td><span class="status pending">Pending</span></td>
<td><span class="status tinggi">Risiko Tinggi</span></td>
<td>
<a href="/admin/laporan/detail/PRM-008" class="btn-detail">Detail</a>
</td>
</tr>

<tr>
<td>PRM-007</td>
<td>Mira Agustiansyah</td>
<td>Cold Work</td>
<td>Pemotongan</td>
<td>UNIT 700</td>
<td class="tgl">06 Maret 2026</td> 
<td><span class="status disetujui">Disetujui</span></td>
<td><span class="status rendah">Risiko Rendah</span></td>
<td>
<a href="/admin/laporan/detail/PRM-007" class="btn-detail">Detail</a>
</td>
</tr>

<tr>
<td>PRM-006</td>
<td>Fikri Ramadhan</td>
<td>Cold Work</td>
<td>Pemotongan</td>
<td>UNIT 700</td>
<td class="tgl">02 Maret 2026</td>
<td><span class="status selesai">Selesai</span></td>
<td><span class="status rendah">Risiko Rendah</span></td>
<td>
<a href="/admin/detail_permit/PRM-006" class="btn-detail">Detail</a>
</td>
</tr>

<tr>
<td>PRM-005</td>
<td>Mira Agustiansyah</td>
<td>Hot Work</td>
<td>Perbaikan panel listrik</td>
<td>UNIT 1500</td>
<td class="tgl">21 Februari 2026</td>
<td><span class="status selesai">Selesai</span></td>
<td><span class="status sedang">Risiko Sedang</span></td>
<td><a href="/admin/detail_permit/PRM-005" class="btn-detail">Detail</a></td>
</tr>

<tr>
<td>PRM-004</td>
<td>Bima Saputra</td>
<td>Cold Work</td>
<td>Perbaikan atap</td>
<td>UNIT 1500</td>
<td class="tgl">11 Februari 2026</td>
<td><span class="status ditolak">Ditolak</span></td>
<td><span class="status tinggi">Risiko Tinggi</span></td>
<td><a href="/admin/detail_permit/PRM-004" class="btn-detail">Detail</a></td>
</tr>

<tr>
<td>PRM-003</td>
<td>Mira Agustiansyah</td>
<td>Hot Work</td>
<td>Pengelasan rangka mesin</td>
<td>UNIT 1500</td>
<td class="tgl">06 Februari 2026</td>
<td><span class="status selesai">Selesai</span></td>
<td><span class="status tinggi">Risiko Tinggi</span></td>
<td><a href="/admin/detail_permit/PRM-003" class="btn-detail">Detail</a></td>
</tr>

<tr>
<td>PRM-002</td>
<td>Dinda Cahya</td>
<td>Cold Work</td>
<td>Perbaikan gudang</td>
<td>UNIT 1500</td>
<td class="tgl">29 Januari 2026</td>
<td><span class="status selesai">Selesai</span></td>
<td><span class="status sedang">Risiko Sedang</span></td>
<td><a href="/admin/detail_permit/PRM-002" class="btn-detail">Detail</a></td>
</tr>

<tr>
<td>PRM-001</td>
<td>Mira Agustiansyah</td>
<td>Hot Work</td>
<td>Pengelasan rangka</td>
<td>UNIT 1500</td>
<td class="tgl">16 Januari 2026</td>
<td><span class="status selesai">Selesai</span></td>
<td><span class="status tinggi">Risiko Tinggi</span></td>
<td><a href="/admin/detail_permit/PRM-001" class="btn-detail">Detail</a></td>
</tr>

</tbody>

</table>
</div>

</div>

</div>

<script>

// ambil semua input
const startInput = document.getElementById("startDate");
const statusInput = document.getElementById("filterStatus");
const summaryItems = document.querySelectorAll(".summary-item");

// event jalan
startInput.addEventListener("change", filterData);
statusInput.addEventListener("change", filterData);

// =====================
// PARSE TANGGAL INDONESIA
// =====================
function parseTanggal(text){
    const bulan = {
        januari:0, februari:1, maret:2, april:3, mei:4, juni:5,
        juli:6, agustus:7, september:8, oktober:9, november:10, desember:11
    };

    let parts = text.toLowerCase().split(" ");
    return new Date(parts[2], bulan[parts[1]], parts[0]);
}

// =====================
// FILTER UTAMA
// =====================
function filterData(){

    let start = startInput.value ? new Date(startInput.value) : null;
    let status = statusInput.value.toLowerCase();

    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {

        let tampil = true;

        // =====================
        // FILTER TANGGAL (FIX 1 TANGGAL)
        // =====================
        let tglEl = row.querySelector(".tgl");
        if(tglEl && start){

            let tgl = parseTanggal(tglEl.innerText);

            // samakan format (biar ga error timezone)
            tgl.setHours(0,0,0,0);
            start.setHours(0,0,0,0);

            // 🔥 WAJIB SAMA TANGGALNYA
            if(tgl.getTime() !== start.getTime()){
                tampil = false;
            }
        }

        // =====================
        // FILTER STATUS
        // =====================
        let statusEl = row.querySelector("td:nth-child(7) .status");

        let statusClass = [...statusEl.classList].find(c =>
            ["pending","disetujui","ditolak","selesai"].includes(c)
        );

        if(status !== "semua" && statusClass !== status){
            tampil = false;
        }

        row.style.display = tampil ? "" : "none";

    });
}

// =====================
// KLIK SUMMARY (BIAR NYAMBUNG)
// =====================
summaryItems.forEach(item => {
    item.addEventListener("click", () => {

        let status = item.dataset.status;

        statusInput.value = status;

        // highlight aktif
        summaryItems.forEach(i => i.classList.remove("active"));
        item.classList.add("active");

        filterData();
    });
});

</script>

</body>
</html>