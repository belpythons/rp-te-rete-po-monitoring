<!DOCTYPE html>
<html>
<head>

<title>Detail Permit - PT KMI</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

body{
background:#eef1f5;
font-family:'Segoe UI', sans-serif;
margin:0;
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
background:#3e6e8f;
color:white;
padding:12px 25px;
border-radius:30px;
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
}

/* CONTAINER */
.container-box{
background:#e5e5e5;
padding:15px;
border-radius:12px;
}

/* HEADER */
.header{
display:flex;
justify-content:space-between;
font-size:13px;
margin-bottom:8px;
}

/* SECTION */
.section{
background:#ffffff;
border-radius:10px;
margin-top:10px;
border:1px solid #cfcfcf;
}

.section-title{
padding:8px 12px;
font-weight:600;
font-size:14px;
border-bottom:1px solid #cfcfcf;
display:flex;
align-items:center;
gap:8px;
}

.section-body{
padding:8px 12px;
font-size:13px;
background:#f8f9fa;
box-shadow: inset 0 0 0 1px #e0e0e0;
}

/* GRID */
.grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:6px 30px;
}

/* ITEM */
.item{
display:flex;
padding:4px 0;
border-bottom:1px solid #dcdcdc;
}

.label{
width:170px;
color:#555;
}

.value{
font-weight:500;
}

/* STATUS */
.badge-status{
padding:3px 10px;
border-radius:6px;
font-size:11px;
color:white;
}

.ditolak{background:#e74c3c;}
.disetujui{background:#27ae60;}
.pending{background:#f39c12;}
.selesai{background:#68ABC2;}

/* RISIKO */
.risk{
display:flex;
justify-content:space-between;
align-items:center;
}

.risk-box{
    color:white;
    padding:3px 10px;
    border-radius:6px;
    font-size:11px;
}

/* WARNA RISIKO */
.risk-tinggi{ background:#e74c3c; }   /* merah */
.risk-sedang{ background:#F9BA2D; color:rgb(255, 255, 255); } /* kuning */
.risk-rendah{ background:#257930; }   /* hijau */

/* CHECKLIST */
.checklist{
list-style:none;
padding-left:0;
margin:0;
}

.checklist li{
margin-bottom:2px;
}

.checklist li::before{
content:"✔ ";
color:green;
font-weight:bold;
}

/* CATATAN */
.note-yellow{
background:#fff3cd;
border:1px solid #f1c40f;
padding:10px;
border-radius:8px;
}

.note-red{
background:#f8d7da;
border:1px solid #e74c3c;
padding:10px;
border-radius:8px;
}

.section-title i.icon-blue{
    color:#0015ff !important;
}

.section-title i.icon-red{
    color:#e74c3c !important;
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

<a href="/admin/riwayat_permit" class="active">
<i class="bi bi-clock-history"></i> Riwayat Permit
</a>

<a href="/admin/laporan">
<i class="bi bi-bar-chart"></i> Laporan
</a>

<div class="logout">
<form method="POST" action="/logout">
@csrf
<button class="btn btn-danger w-100 mb-2">Logout</button>
</form>
<span style="font-size:14px;">Admin</span>
</div>

</div>

<!-- CONTENT -->
<div class="content">

<div class="topbar">
<b>PERMIT TO WORK - PT.KMI</b>
<div><i class="bi bi-bell"></i> Admin</div>
</div>

<h5><b>Detail Permit</b></h5>

<div class="container-box">

<!-- HEADER -->
<div class="header">
<div>Nomor Permit : <b>{{ $permit['nomor'] ?? '-' }}</b></div>
<div>Status :
<span class="badge-status {{ strtolower($permit['status'] ?? '') }}">
{{ $permit['status'] ?? '-' }}
</span>
</div>
</div>

<!-- INFORMASI -->
<div class="section">
<div class="section-title">
<i class="bi bi-file-earmark-text icon-blue"></i> Informasi Permit
</div>

<div class="section-body">
<div class="grid">

<div class="item"><div class="label">Nomor Permit</div><div class="value">: {{ $permit['nomor'] ?? '-' }}</div></div>
<div class="item"><div class="label">Jenis Pekerjaan</div><div class="value">: {{ $permit['jenis'] ?? '-' }}</div></div>

<div class="item"><div class="label">Tanggal Pelaksanaan</div><div class="value">: {{ $permit['tgl_kerja'] ?? '-' }}</div></div>
<div class="item"><div class="label">Pekerjaan</div><div class="value">: {{ $permit['pekerjaan'] ?? '-' }}</div></div>

<div class="item"><div class="label">Waktu Pelaksanaan</div><div class="value">: {{ $permit['waktu'] ?? '-' }}</div></div>

</div>
</div>
</div>

<!-- PEKERJA -->
<div class="section">
<div class="section-title">
<i class="bi bi-person icon-blue"></i> Informasi Pekerja
</div>

<div class="section-body">
<div class="grid">

<div class="item"><div class="label">Nama Pekerja</div><div class="value">: {{ $permit['nama'] ?? '-' }}</div></div>
<div class="item"><div class="label">Departemen</div><div class="value">: {{ $permit['departemen'] ?? '-' }}</div></div>

<div class="item"><div class="label">Section</div><div class="value">: {{ $permit['section'] ?? '-' }}</div></div>
<div class="item"><div class="label">Supervisor</div><div class="value">: {{ $permit['supervisor'] ?? '-' }}</div></div>

</div>
</div>
</div>

<!-- LOKASI -->
<div class="section">
<div class="section-title">
<i class="bi bi-geo-alt icon-blue"></i> Lokasi Pekerjaan
</div>

<div class="section-body">
<div class="grid">

<div class="item"><div class="label">Gedung</div><div class="value">: {{ $permit['gedung'] ?? '-' }}</div></div>
<div class="item"><div class="label">Lokasi</div><div class="value">: {{ $permit['lokasi'] ?? '-' }}</div></div>

</div>
</div>
</div>

<!-- DESKRIPSI -->
<div class="section">
<div class="section-title">
<i class="bi bi-card-text icon-blue"></i>Deskripsi Pekerjaan
</div>

<div class="section-body">
{{ $permit['deskripsi'] ?? 'Tidak ada deskripsi' }}
</div>
</div>

<!-- RISIKO -->
<div class="section">
<div class="section-title">
<i class="bi bi-exclamation-triangle icon-red"></i> Tingkat Risiko
</div>

<div class="section-body">
<div class="risk">

<div>
<ul class="checklist">
@foreach($permit['apd'] ?? [] as $item)
<li>{{ $item }}</li>
@endforeach
</ul>
</div>

<div class="risk-box 
    @if(str_contains(strtolower($permit['risiko'] ?? ''), 'tinggi')) risk-tinggi
    @elseif(str_contains(strtolower($permit['risiko'] ?? ''), 'sedang')) risk-sedang
    @elseif(str_contains(strtolower($permit['risiko'] ?? ''), 'rendah')) risk-rendah
    @endif
">
{{ $permit['risiko'] ?? '-' }}
</div>

</div>
</div>
</div>

<!-- CATATAN SUPERVISOR -->
<div class="section">
<div class="section-title">
<i class="bi bi-pencil-square icon-red"></i> Catatan Supervisor
</div>

<div class="section-body">
<div class="note-yellow">
{{ $permit['catatan']['supervisor'] ?? 'Tidak ada catatan' }}
</div>
</div>
</div>

<!-- CATATAN SAFETY -->
<div class="section">
<div class="section-title">
<i class="bi bi-pencil-square icon-red"></i> Catatan Safety Officer
</div>

<div class="section-body">
<div class="note-red">
{{ $permit['catatan']['safety'] ?? 'Tidak ada catatan' }}
</div>
</div>
</div>

</div>
</div>

</body>
</html>