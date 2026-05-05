<!DOCTYPE html>
<html>
<head>

<title>Monitoring Permit - PT.KMI</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

body{
font-family:'Segoe UI', sans-serif;
margin:0;

/* 🔥 BACKGROUND SAMA */
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

/* ✅ FIX */
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

/* ✅ FIX */
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
/* 🔥 KONSISTEN DENGAN HALAMAN USER */
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
padding:22px;
margin-top:20px;
box-shadow:0 3px 8px rgba(0,0,0,0.05);

/* 🔥 TAMBAHAN + FIX */
position:relative;
z-index:3;
}

/* TABLE */
.table{
border-radius:12px;
overflow:hidden;
}

.table thead{
background:#f4f6f9;
font-weight:600;
}

.table tbody tr:hover{
background:#f9fbfd;
}

/* STATUS */
.status{
display:inline-block;
width:110px;
text-align:center;
padding:6px 0;
border-radius:20px;
font-size:13px;
font-weight:600;
}

.pending{background:#f7d794;color:#5a3d00;}
.disetujui{background:#78c28c;color:white;}
.ditolak{background:#e74c3c;color:white;}
.selesai{background:#6c8cd5;color:white;}
.aktif{background:#4e79c7;color:white;}

/* BUTTON */
.btn-detail{
background:#4e79c7;
color:white;
border-radius:8px;
padding:5px 12px;
font-size:13px;
border:none;
text-decoration:none;
display:inline-block;
}

.btn-detail:hover{
background:#3b63a6;
color:white;
}

table th, table td{
text-align:center;
vertical-align:middle;
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

<a class="active" href="{{ route('admin.monitoring') }}">
<i class="bi bi-clipboard-data"></i> Monitoring Permit
</a>

<a href="{{ url('/admin/riwayat_permit') }}">
<i class="bi bi-clock-history"></i> Riwayat Permit
</a>

<a href="{{ url('/admin/laporan') }}">
<i class="bi bi-bar-chart"></i> Laporan
</a>

<div class="logout">
<form method="POST" action="/logout">
@csrf
<button type="submit" class="btn btn-danger w-100 mb-2">Logout</button>
</form>
<span style="font-size:14px;">Admin</span>
</div>

</div>

<!-- CONTENT -->
<div class="content">

<!-- TOPBAR -->
<div class="topbar">
<b>PERMIT TO WORK - PT.KMI</b>

<div class="user-info">
<i class="bi bi-bell"></i>
<span>Admin</span>
<img src="/images/me.jpg" class="user-avatar">
</div>
</div>

<h4 class="mt-4" style="color:#000000;">
    <b>Monitoring Permit</b>
</h4>

<p style="color:#000000;">
    Kelola data permit kerja dalam sistem Permit to Work
</p>

<div class="card-box">

<!-- FILTER -->
<div class="d-flex align-items-center mb-3 gap-2">
<label>Filter Status:</label>
<select id="filterStatus" class="form-select" style="width:200px;">
<option value="">Semua Status</option>
<option value="Pending">Pending</option>
<option value="Disetujui">Disetujui</option>
<option value="Aktif">Aktif</option>
<option value="Ditolak">Ditolak</option>
<option value="Selesai">Selesai</option>
</select>
</div>

<!-- TABLE -->
<table class="table" id="permitTable">

<thead>
<tr>
<th>No.Permit</th>
<th>Nama</th>
<th>Lokasi</th>
<th>Jenis Pekerjaan</th>
<th>Tanggal Kerja</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
@foreach($permits as $permit)
<tr>
<td>{{ $permit['nomor'] }}</td>
<td>{{ $permit['nama'] }}</td>
<td>{{ $permit['lokasi'] }}</td>
<td>{{ $permit['jenis'] }}</td>
<td>{{ $permit['tanggal'] }}</td>

<td>
<span class="status 
{{ strtolower($permit['status']) == 'pending' ? 'pending' : '' }}
{{ strtolower($permit['status']) == 'disetujui' ? 'disetujui' : '' }}
{{ strtolower($permit['status']) == 'aktif' ? 'aktif' : '' }}
{{ strtolower($permit['status']) == 'ditolak' ? 'ditolak' : '' }}">
{{ $permit['status'] }}
</span>
</td>

<td>
<a href="{{ route('admin.monitoring.detail', $permit['id']) }}" class="btn-detail">
Detail
</a>
</td>

</tr>
@endforeach
</tbody>


</table>

</div>

</div>

<script>

let filter = document.getElementById("filterStatus");

filter.addEventListener("change", filterData);

function filterData(){

let status = filter.value.toLowerCase();
let rows = document.querySelectorAll("#permitTable tbody tr");

rows.forEach(row => {

let statusText = row.children[5].innerText.toLowerCase();
let cocokStatus = status === "" || statusText.includes(status);

row.style.display = cocokStatus ? "" : "none";

});

}

</script>

</body>
</html>