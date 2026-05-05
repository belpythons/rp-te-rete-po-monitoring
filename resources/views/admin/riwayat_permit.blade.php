<!DOCTYPE html> 
<html>
<head>

<title>Riwayat Permit - PT KMI</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

body{
font-family:'Segoe UI', sans-serif;
margin:0;

/* 🔥 BACKGROUND SAMA SEPERTI USERS */
background: url("{{ asset('images/kmi2.jpg') }}") no-repeat center center fixed;
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
background: rgba(255,255,255,0.95);
backdrop-filter: blur(6px);
border-radius:18px;
padding:22px;
margin-top:20px;
box-shadow:0 3px 8px rgba(0,0,0,0.05);
}

table th, table td{
text-align:center;
vertical-align:middle;
}

/* STATUS BADGE */
.status-ditolak{
background:#e74c3c;
color:white;
padding:5px 14px;
border-radius:8px;
font-size:12px;
}

.status-selesai{
background:#5dade2;
color:white;
padding:5px 14px;
border-radius:8px;
font-size:12px;
}

.status-expired{
background:#aab2bd;
color:white;
padding:5px 14px;
border-radius:8px;
font-size:12px;
}

.btn-detail{
background:#4e79c7;
color:white;
border:none;
}

</style>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

<div class="sidebar-header">
<img src="/images/kmi-logo.png">
</div>

<a href="/admin/dashboard">
<i class="bi bi-speedometer2"></i> Dashboard
</a>

<a href="/admin/users">
<i class="bi bi-people"></i> Manajemen User
</a>

<a href="/admin/monitoring_permit">
<i class="bi bi-clipboard-data"></i> Monitoring Permit
</a>

<a class="active" href="/admin/riwayat_permit">
<i class="bi bi-clock-history"></i> Riwayat Permit
</a>

<a href="/admin/laporan">
<i class="bi bi-bar-chart"></i> Laporan
</a>

<div class="logout">

<form method="POST" action="/logout">
<button type="submit" class="btn btn-danger w-100 mb-2">
Logout
</button>
</form>

<span style="font-size:14px;">Admin</span>

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

<h4 class="mt-4" style="color:#000000;"><b>Riwayat Permit</b></h4>
<p style="color:#000000;">Kelola daftar departemen kerja dalam sistem Permit to Work</p>

<div class="card-box">

<!-- FILTER -->
<div class="mb-3" style="max-width:300px;">
<label>Filter Status:</label>
<select id="filterStatus" class="form-select">
<option value="">Semua Status</option>
<option value="ditolak">Ditolak</option>
<option value="selesai">Selesai</option>
<option value="expired">Expired</option>
</select>
</div>

<table class="table table-bordered" id="permitTable">

<thead>
<tr>
<th>No. Permit</th>
<th>Nama</th>
<th>Lokasi</th>
<th>Jenis Pekerjaan</th>
<th>Tanggal Kerja</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

<tr>
<td>PRM-009</td>
<td>Dinda Cahya</td>
<td>UNIT 1200 – FIRE TANK</td>
<td>Permit Confined Space</td>
<td>03 April 2026</td>
<td><span class="status-ditolak">Ditolak</span></td>
<td>
<a href="/admin/detail_permit/PRM-009" class="btn btn-sm btn-detail">
<i class="bi bi-search"></i> Detail
</a>
</td>
</tr>

<tr>
<td>PRM-006</td>
<td>Fikri Ramadhan</td>
<td>UNIT 700 – MAIN</td>
<td>Cold Work Permit</td>
<td>02 Maret 2026</td>
<td><span class="status-selesai">Selesai</span></td>
<td>
<a href="/admin/detail_permit/PRM-006" class="btn btn-sm btn-detail">
<i class="bi bi-search"></i> Detail
</a>
</td>
</tr>

<tr>
<td>PRM-005</td>
<td>Mira Agustiansyah</td>
<td>UNIT 1500 – CONTROL</td>
<td>Listrik & Instrument</td>
<td>21 Februari 2026</td>
<td><span class="status-selesai">Selesai</span></td>
<td>
<a href="/admin/detail_permit/PRM-005" class="btn btn-sm btn-detail">
<i class="bi bi-search"></i> Detail
</a>
</td>
</tr>

<tr>
<td>PRM-004</td>
<td>Bima Saputra</td>
<td>UNIT 1500 – CONTROL</td>
<td>Cold Work Permit</td>
<td>11 Februari 2026</td>
<td><span class="status-ditolak">Ditolak</span></td>
<td>
<a href="/admin/detail_permit/PRM-004" class="btn btn-sm btn-detail">
<i class="bi bi-search"></i> Detail
</a>
</td>
</tr>

<tr>
<td>PRM-003</td>
<td>Mira Agustiansyah</td>
<td>UNIT 1500 – CONTROL</td>
<td>Hot Work Permit</td>
<td>06 Februari 2026</td>
<td><span class="status-selesai">Selesai</span></td>
<td>
<a href="/admin/detail_permit/PRM-003" class="btn btn-sm btn-detail">
<i class="bi bi-search"></i> Detail
</a>
</td>
</tr>

<tr>
<td>PRM-002</td>
<td>Dinda Cahya</td>
<td>UNIT 1150 – LOADING</td>
<td>Cold Work Permit</td>
<td>29 Januari 2026</td>
<td><span class="status-selesai">Selesai</span></td>
<td>
<a href="/admin/detail_permit/PRM-002" class="btn btn-sm btn-detail">
<i class="bi bi-search"></i> Detail
</a>
</td>
</tr>

<tr>
<td>PRM-001</td>
<td>Mira Agustiansyah</td>
<td>UNIT 1500 – CONTROL</td>
<td>Hot Work Permit</td>
<td>16 Januari 2026</td>
<td><span class="status-selesai">Selesai</span></td>
<td>
<a href="/admin/detail_permit/PRM-001" class="btn btn-sm btn-detail">
<i class="bi bi-search"></i> Detail
</a>
</td>
</tr>

</tbody>

</table>

</table>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

let filter = document.getElementById("filterStatus");

filter.addEventListener("change", function(){

let value = this.value.toLowerCase();
let rows = document.querySelectorAll("#permitTable tbody tr");

rows.forEach(function(row){

let status = row.children[5].innerText.toLowerCase();

if(value === "" || status.includes(value)){
row.style.display="";
}else{
row.style.display="none";
}

});

});

</script>

</body>
</html>