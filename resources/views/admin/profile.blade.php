    <!DOCTYPE html>
    <html>
    <head>

    <title>Profile - PT KMI</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>

    body{
    font-family:Segoe UI;
    margin:0;
    background: url("{{ asset('images/kmi2.jpg') }}") no-repeat center center fixed;
    background-size: cover;
    }

    /* SIDEBAR (Disesuaikan dengan Dashboard) */
.sidebar {
    width: 240px; /* Dashboard menggunakan 240px */
    height: 100vh;
    background: #2f3f52; /* Warna dashboard */
    position: fixed;
    color: white;
    display: flex;
    flex-direction: column;
    z-index: 2;
}

.sidebar-header {
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.sidebar-header img {
    width: 140px; /* Ukuran dashboard */
}

.sidebar a {
    display: block;
    padding: 14px 22px;
    color: #dce4ec;
    text-decoration: none;
    font-size: 15px; /* Dashboard menggunakan 15px */
}

.sidebar a:hover {
    background: #3e5670;
}

.sidebar .active {
    background: #456ea4; /* Warna active dashboard */
    color: white;
}

/* Bagian Logout */
.logout {
    margin-top: auto;
    padding: 20px;
    text-align: center;
}

/* CONTENT ADJUSTMENT */
.content {
    margin-left: 240px; /* Samakan dengan width sidebar */
    padding: 25px;
    position: relative;
    z-index: 2;
}

/* TOPBAR ADJUSTMENT */
.topbar {
    background: rgba(22, 61, 87, 0.9); /* Transparansi dashboard */
    backdrop-filter: blur(6px);
    color: white;
    padding: 12px 25px;
    border-radius: 30px; /* Dashboard bulat 30px */
    display: flex;
    justify-content: space-between;
    align-items: center;
}

    .topbar-right{
    display:flex;
    align-items:center;
    gap:12px;
    }

    .topbar-right img{
    width:35px;
    height:35px;
    border-radius:50%;
    object-fit:cover;
    }

    /* CARD */
    .profile-card{
    background:#f8f8f8;
    border-radius:6px;
    padding:20px;
    margin-top:15px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
    }

    .profile-title{
    font-size:24px;
    font-weight:600;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:10px;
    }

    /* WRAP */
    .profile-wrapper{
    display:flex;
    gap:30px;
    }

    /* LEFT */
    .profile-left{
    width:220px;
    text-align:center;
    }

    .profile-left img{
    width:160px;
    height:160px;
    border-radius:50%;
    object-fit:cover;
    }

    .upload-btn{
    margin-top:15px;
    background:#5d7fd4;
    color:white;
    padding:8px 18px;
    border-radius:3px;
    cursor:pointer;
    display:inline-block;
    font-size:14px;
    }

    .upload-btn:hover{
    background:#4b6dc2;
    }

    /* RIGHT */
    .profile-right{
    flex:1;
    }

    .form-group{
    display:flex;
    align-items:center;
    margin-bottom:12px;
    }

    .form-group label{
    width:150px;
    font-weight:600;
    font-size:15px;
    color:#4d4d4d;
    }

    .form-control{
    border-radius:2px;
    height:34px;
    font-size:14px;
    }

    .status-badge{
    background:#d9f0d9;
    color:#2f8f2f;
    padding:4px 14px;
    border-radius:4px;
    font-size:14px;
    }

    /* PASSWORD */
    .password-box{
    background:#eef0f4;
    padding:8px 12px;
    border-radius:3px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border:1px solid #ddd;
    }

    .password-box a{
    text-decoration:none;
    font-size:14px;
    }

    /* BUTTON */
    .action-buttons{
    display:flex;
    justify-content:flex-end;
    gap:0;
    margin-top:30px;
    }

    .btn-cancel{
    background:#d9d9d9;
    border:none;
    padding:10px 50px;
    border-radius:0;
    }

    .btn-save{
    background:#2cab43;
    color:white;
    border:none;
    padding:10px 50px;
    border-radius:0;
    }

    </style>

    </head>

    <body>

    <!-- SIDEBAR -->
 <div class="sidebar">

    <div class="sidebar-header">
        <img src="/images/kmi-logo.png">
    </div>

    <a href="/admin/dashboard" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <a href="/admin/users" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Manajemen User
    </a>

    <a href="{{ route('admin.monitoring') }}" class="{{ request()->is('admin/monitoring*') ? 'active' : '' }}">
        <i class="bi bi-clipboard-data"></i> Monitoring Permit
    </a>

    <a href="/admin/riwayat_permit" class="{{ request()->is('admin/riwayat_permit') ? 'active' : '' }}">
        <i class="bi bi-clock-history"></i> Riwayat Permit
    </a>

    <a href="/admin/laporan" class="{{ request()->is('admin/laporan') ? 'active' : '' }}">
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

<div class="topbar">
    <div>
        <b>PERMIT TO WORK - PT.KMI</b>
        
    </div>

    <div class="topbar-right">
    <i class="bi bi-bell-fill"></i>

    <span>Admin</span>

    <img src="{{ auth()->user()->foto ? asset('uploads/'.auth()->user()->foto) : asset('images/me.jpg') }}">
</div>
</div>

    @if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="/admin/update-profile" enctype="multipart/form-data">
    @csrf

    <div class="profile-card">

    <div class="profile-title">
    <i class="bi bi-person"></i> Informasi Profil
    </div>

    <div class="profile-wrapper">

    <!-- FOTO -->
    <div class="profile-left">

    <img id="previewFoto"
    src="{{ auth()->user()->foto ? asset('uploads/'.auth()->user()->foto) : asset('images/me.jpg') }}">

    <br>

    <label class="upload-btn">
    <i class="bi bi-camera"></i> Ubah Foto
    <input type="file" id="inputFoto" name="foto" hidden>
    </label>

    </div>

    <!-- FORM -->
    <div class="profile-right">

    <div class="form-group">
    <label>Nama Lengkap</label>
    <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}">
    </div>

    <div class="form-group">
    <label>Username</label>
    <input type="text" class="form-control" name="username" value="{{ auth()->user()->username }}">
    </div>

    <div class="form-group">
    <label>Email</label>
    <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}">
    </div>

    <div class="form-group">
    <label>Role</label>
    <input type="text" class="form-control" value="{{ auth()->user()->role }}" readonly>
    </div>

    <div class="form-group">
    <label>Departemen</label>
    <input type="text" class="form-control" value="HR & IS Dept">
    </div>

    <div class="form-group">
    <label>Section</label>
    <input type="text" class="form-control" value="IT">
    </div>

    <div class="form-group">
    <label>Status</label>
    <span class="status-badge">Aktif</span>
    </div>

    <div class="form-group">
    <label>Password</label>
    <div class="password-box w-100">
    <span>************</span>
    <a href="#" onclick="alert('Fitur belum tersedia')" class="text-primary">
    Ubah Password <i class="bi bi-key"></i>
    </a>
    </div>
    </div>

    <div class="action-buttons">
    <button type="reset" class="btn-cancel">Batal</button>
    <button type="submit" class="btn-save">Simpan</button>
    </div>

    </div>

    </div>

    </div>

    </form>

    </div>

    <script>
    document.getElementById("inputFoto").addEventListener("change", function(e){
    let file = e.target.files[0];
    if(file){
    let reader = new FileReader();
    reader.onload = function(e){
    document.getElementById("previewFoto").src = e.target.result;
    }
    reader.readAsDataURL(file);
    }
    });
    </script>

    </body>
    </html>