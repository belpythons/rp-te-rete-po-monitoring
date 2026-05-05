<!DOCTYPE html>
<html>
<head>

<title>Detail Permit - PT KMI</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

/* ===== GLOBAL ===== */
body{
font-family:'Segoe UI', sans-serif;
margin:0;

/* 🔥 SAMAKAN DENGAN DASHBOARD */
background: url("{{ asset('images/kmi2.jpg') }}") no-repeat center center fixed;
background-size: cover;
}

/* ===== SIDEBAR ===== */
.sidebar{
width:240px;
height:100vh;
background:#2f3f52;
position:fixed;
top:0;
left:0;
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

/* ===== CONTENT ===== */
.content{
margin-left:240px;
padding:25px;
position:relative;
z-index:2;
}

/* ===== TOPBAR ===== */
.topbar{
background: rgba(22, 61, 87, 0.9);
backdrop-filter: blur(6px);
color:white;
padding:12px 25px;
border-radius:30px;
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
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

/* ===== CARD ===== */
.card-box{
background:white;
border-radius:18px;
padding:18px;
box-shadow:0 3px 8px rgba(0,0,0,0.05);
}

/* ===== HEADER ===== */
.header-bar{
background:#dcdcdc;
padding:12px 18px;
border-radius:10px;
display:flex;
justify-content:space-between;
align-items:center;
font-size:14px;
margin-bottom:12px;
}

/* ===== SECTION ===== */
.section{
background:#ffffff;
border-radius:10px;
margin-top:10px;
border:1px solid #dcdcdc;
overflow:hidden;
}

.section-title{
padding:8px 12px;
background:#f1f3f5;
border-bottom:1px solid #dcdcdc;
font-weight:600;
display:flex;
align-items:center;
gap:8px;
font-size:14px;
}

.section-body{
padding:12px 15px;
}

.grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:8px 30px;
font-size:13px;
}

.item{
display:flex;
gap:10px;
}

.label{
width:150px;
color:#555;
}

.value{
font-weight:500;
}

/* ===== STATUS ===== */
.status{
padding:4px 10px;
border-radius:6px;
font-size:12px;
font-weight:600;
}

.pending{background:#f39c12;color:white;}
.disetujui{background:#27ae60;color:white;}
.ditolak{background:#e74c3c;color:white;}
.aktif{background:#3498db;color:white;}

/* ===== RISIKO ===== */
.risk-box{
display:flex;
justify-content:space-between;
align-items:center;
}

.risk-label{
background:#e74c3c;
color:white;
padding:5px 12px;
border-radius:6px;
font-size:12px;
}

/* ===== CATATAN ===== */
.note{
background:#f8d7da;
border:1px solid #e74c3c;
border-radius:8px;
padding:12px;
font-size:13px;
line-height:1.6;
}

/* ✅ TAMBAHKAN DI SINI */
.note-yellow{
background:#ffeaa7;
border:1px solid #e1c062;
border-radius:8px;
padding:12px;
margin-top:10px;
font-size:13px;
}

.note-red{
background:#f8d7da;
border:1px solid #e74c3c;
border-radius:8px;
padding:12px;
margin-top:10px;
font-size:13px;
}

/* PROFILE DROPDOWN */
.profile-box{
position:absolute;
top:55px;
right:0;
width:260px;
background:white;
border-radius:15px;
box-shadow:0 8px 20px rgba(0,0,0,0.15);
overflow:hidden;
z-index:999;
opacity:0;
transform:translateY(-10px);
transition:0.2s;
pointer-events:none;
}

.profile-box.show{
opacity:1;
transform:translateY(0);
pointer-events:auto;
}

/* HEADER */
.profile-header{
display:flex;
align-items:center;
gap:12px;
padding:15px;
background:#f1f1f1;
border-bottom:1px solid #ddd;
}

.profile-header img{
width:45px;
height:45px;
border-radius:50%;
}

/* TEXT */
.profile-text{
display:flex;
flex-direction:column;
justify-content:center;
}

.profile-text b{
font-size:14px;
color:#000 !important;
font-weight:600;
margin:0;
}

.profile-text small{
font-size:12px;
color:#000 !important;
opacity:0.7;
margin:0;
}

/* BUTTON */
.btn-edit{
display:block;
background:#6d8f73;
color:white;
text-align:center;
padding:12px;
font-size:14px;
text-decoration:none;
}

.btn-edit:hover{
background:#5a7860;
}

.btn-logout{
width:100%;
background:#d9534f;
color:white;
border:none;
padding:12px;
font-size:14px;
}

.btn-logout:hover{
background:#c9302c;
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

<a href="/admin/monitoring_permit"class="active">
<i class="bi bi-clipboard-data"></i> Monitoring Permit
</a>

<a href="/admin/riwayat_permit">
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

<!-- TOPBAR -->
<div class="topbar">
<b>PERMIT TO WORK - PT.KMI</b>

<!-- DROPDOWN PROFIL -->
<div id="profileBox" class="profile-box d-none" onclick="event.stopPropagation()">

    <div class="profile-header">
        <img src="/images/me.jpg">
        <div class="profile-text">
            <b>ayuu03</b>
            <small>ayuu17@outlook.com</small>
        </div>
    </div>

    <a href="#" class="btn-edit">
        <i class="bi bi-pencil"></i> Edit Profil
    </a>

    <form method="POST" action="/logout">
        <button class="btn-logout">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>

</div>

<div class="user-info">
<i class="bi bi-bell"></i>
<span>Admin</span>
<img src="/images/me.jpg" class="user-avatar">
</div>
</div>

<h4>Detail Permit</h4>
<p>Informasi lengkap permit kerja</p>

<div class="card-box">

<!-- HEADER -->
<div class="header-bar">
<div>Nomor Permit : <b>{{ $permit['nomor'] }}</b></div>
<div>Status : 
<span class="status 
{{ strtolower($permit['status']) == 'pending' ? 'pending' : '' }}
{{ strtolower($permit['status']) == 'disetujui' ? 'disetujui' : '' }}
{{ strtolower($permit['status']) == 'ditolak' ? 'ditolak' : '' }}
{{ strtolower($permit['status']) == 'aktif' ? 'aktif' : '' }}">
{{ $permit['status'] }}
</span>
</div>
</div>

<!-- INFORMASI -->
<div class="section">
<div class="section-title">
<i class="bi bi-file-earmark-text" style="color:#3877CD;"></i> Informasi Permit
</div>
<div class="section-body">
<div class="grid">
<div class="item"><div class="label">Nomor Permit</div><div class="value">: {{ $permit['nomor'] }}</div></div>
<div class="item"><div class="label">Jenis Pekerjaan</div><div class="value">: {{ $permit['jenis'] }}</div></div>
<div class="item"><div class="label">Tanggal Pelaksanaan</div><div class="value">: {{ $permit['tgl_kerja'] }}</div></div>
<div class="item"><div class="label">Pekerjaan</div><div class="value">: {{ $permit['pekerjaan'] }}</div></div>
<div class="item"><div class="label">Waktu</div><div class="value">: {{ $permit['waktu'] }}</div></div>
</div>
</div>
</div>

<!-- PEKERJA -->
<div class="section">
<div class="section-title">
<i class="bi bi-person" style="color:#3877CD;"></i> Informasi Pekerja
</div>
<div class="section-body">
<div class="grid">
<div class="item"><div class="label">Nama Pekerja</div><div class="value">: {{ $permit['nama'] }}</div></div>
<div class="item"><div class="label">Departemen</div><div class="value">: {{ $permit['departemen'] }}</div></div>
<div class="item"><div class="label">Section</div><div class="value">: {{ $permit['section'] }}</div></div>
<div class="item"><div class="label">Supervisor</div><div class="value">: {{ $permit['supervisor'] }}</div></div>
</div>
</div>
</div>

<!-- LOKASI -->
<div class="section">
<div class="section-title">
<i class="bi bi-geo-alt" style="color:#3877CD;"></i> Lokasi Pekerjaan
</div>
<div class="section-body">
<div class="grid">
<div class="item"><div class="label">Gedung</div><div class="value">: {{ $permit['gedung'] }}</div></div>
<div class="item"><div class="label">Area</div><div class="value">: {{ $permit['area'] }}</div></div>
<div class="item"><div class="label">Lokasi</div><div class="value">: {{ $permit['lokasi'] }}</div></div>
</div>
</div>
</div>

<!-- DESKRIPSI -->
<div class="section">
<div class="section-title">
<i class="bi bi-file-text" style="color:#3877CD;"></i> Deskripsi Pekerjaan
</div>
<div class="section-body">
{{ $permit['deskripsi'] }}
</div>
</div>

<!-- RISIKO -->
<div class="section">
<div class="section-title">
<i class="bi bi-exclamation-circle text-danger"></i> Tingkat Risiko
</div>
<div class="section-body">
<div class="risk-box">

<div>
@foreach($permit['apd'] as $item)
✔ {{ $item }} <br>
@endforeach
</div>

<div class="risk-label">{{ $permit['risiko'] }}</div>

</div>
</div>
</div>

<!-- CATATAN SUPERVISOR -->
<div class="note-yellow">
<i class="bi bi-pencil-square"></i> Catatan Supervisor
<br>
{{ $permit['catatan']['supervisor'] ?? '-' }}
</div>

<!-- CATATAN SAFETY -->
<div class="note-red">
<i class="bi bi-pencil-square"></i> Catatan Safety Officer
<br>
{{ $permit['catatan']['safety'] ?? '-' }}
</div>

</div>

</div>

</body>
</html>