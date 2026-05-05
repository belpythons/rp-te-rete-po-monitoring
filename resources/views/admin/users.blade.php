<!DOCTYPE html> 
<html>
<head>

<title>Manajemen User - PT KMI</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

/* 🔥 FIX BACKGROUND SAAT MODAL DIBUKA */

.modal-backdrop.show{
background: rgba(0,0,0,0.1) !important;
}

/* OPTIONAL BIAR LEBIH HALUS */
.modal-backdrop.show{
backdrop-filter: blur(2px);
}

body{
font-family:'Segoe UI', sans-serif;
margin:0;

/* 🔥 BACKGROUND */
background: url("{{ asset('images/kmi2.jpg') }}") no-repeat center center fixed;
background-size: cover;
}

/* 🔥 OVERLAY */
.overlay{
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(255, 255, 255, 0);
z-index:0;
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
z-index:2;
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
background:rgba(22, 61, 87, 0.9);;
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

.search-box{
display:flex;
gap:10px;
align-items:center;
flex-wrap:wrap;
margin-bottom:15px;
}

.search-box input{
width:200px;
}

.badge-aktif{
background:#4CAF50;
color:white;
padding:5px 12px;
border-radius:20px;
font-size:12px;
}

.badge-nonaktif{
background:#e74c3c;
color:white;
padding:5px 12px;
border-radius:20px;
font-size:12px;
}

.btn-edit{
background:#4e79c7;
color:white;
border:none;
}

.btn-hapus{
background:#e74c3c;
color:white;
border:none;
}

table th, table td{
text-align:center;
vertical-align:middle;
}

.role{
text-transform: capitalize;
}

/* MODAL */
.custom-modal{
max-width: 350px;
width: 100%;
}

.custom-modal-content{
max-width: 350px;
width: 100%;
}

.modal-content{
border-radius:12px;
border:none;
overflow:hidden;
box-shadow:0 8px 20px rgba(0,0,0,0.25);
}

/* HEADER */
.modal-header{
background:#f1f1f1;
padding:10px 12px;
border-bottom:1px solid #e0e0e0;
}

.modal-header h5{
font-size:13px;
font-weight:600;
margin:0;
}

.modal-header .btn-close{
width:24px;
height:24px;
font-size:18px;
opacity:0.7;
}

.modal-header .btn-close:hover{
opacity:1;
}

/* BODY */
.modal-body{
background:#f7f7f7;
padding:12px;
}

/* FORM */
.form-box{
background:#eaeaea;
padding:10px;
border-radius:6px;
}

/* LABEL */
.form-box label{
font-size:11px;
margin-bottom:4px;
display:block;
font-weight:500;
color:#333;
}

/* INPUT */
.form-box input,
.form-box select{
width:100%;
height:32px;
font-size:12px;
margin-bottom:8px;
border-radius:4px;
border:1px solid #ccc;
padding:4px 8px;
box-sizing:border-box;
}

/* ICON DALAM INPUT */
.input-icon{
position:relative;
margin-bottom:8px;
}

.input-icon i{
position:absolute;
top:50%;
transform:translateY(-50%);
left:10px;
color:#999;
font-size:14px;
pointer-events:none;
z-index:5;
}

.input-icon input{
padding-left:40px;
width:100%;
}

.input-icon input.form-control{
padding-left:40px !important;
appearance: none;
-webkit-appearance: none;
-moz-appearance: none;
background-color: #fff !important;
background-image: none !important;
border-left:2px solid #ddd;
}

.input-icon input.form-control::placeholder{
color:#ccc;
}

/* FOOTER */
.modal-footer{
padding:8px;
background:#f1f1f1;
display:flex;
gap:8px;
}

.btn-batal{
flex:1;
background:#2d8cff;
color:white;
border:none;
height:32px;
font-size:12px;
border-radius:4px;
cursor:pointer;
font-weight:500;
}

.btn-batal:hover{
background:#1563d9;
}

.btn-update{
flex:1;
background:#f3f3f3;
border:1px solid #ccc;
color:#333;
height:32px;
font-size:12px;
border-radius:4px;
cursor:pointer;
font-weight:500;
}

.btn-update:hover{
background:#e8e8e8;
}

.custom-footer{
gap: 8px;
}

/* INPUT LEBIH COMPACT */
.form-box input.form-control,
.form-box select.form-select{
width:100%;
height:32px;
font-size:12px;
margin-bottom:8px;
border-radius:4px;
border:1px solid #ccc;
padding:4px 8px;
box-sizing:border-box;
}

.form-box input.form-control:focus,
.form-box select.form-select:focus{
outline:none;
border-color:#2d8cff;
box-shadow:0 0 0 2px rgba(45,140,255,0.2);
}

/* =========================
   FIX MODAL AGAR BISA DIKLIK
========================= */

.modal{
z-index: 9999 !important;
}

.modal-backdrop{
z-index: 9998 !important;
}

/* overlay jangan nutup modal */
.overlay{
z-index: 1;
}

/* backdrop lebih halus */
.modal-backdrop.show{
background: rgba(0,0,0,0.5) !important;
backdrop-filter: blur(8px);
-webkit-backdrop-filter: blur(8px);
}

/* efek blur pada background saat modal terbuka */
body.modal-open{
padding-right: 0 !important;
}

body.modal-open::before{
content: '';
position: fixed;
top: 0;
left: 0;
width: 100%;
height: 100%;
background: rgba(0,0,0,0.5);
backdrop-filter: blur(8px);
z-index: 0;
pointer-events: none;
}

/* content tetap fokus di atas blur */
.content{
position: relative;
z-index: 10;
}

</style>

</head>

<body>

<!-- 🔥 OVERLAY -->
<div class="overlay"></div>

<!-- SIDEBAR -->
<div class="sidebar">

<div class="sidebar-header">
<img src="/images/kmi-logo.png">
</div>

<a href="/admin/dashboard">
<i class="bi bi-speedometer2"></i> Dashboard
</a>

<a class="active" href="/admin/users">
<i class="bi bi-people"></i> Manajemen User
</a>

<a href="{{ route('admin.monitoring') }}">
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

<!-- PINDAH KE SINI (DI LUAR TOPBAR) -->
@if(session('success'))
<div id="notifSuccess" style="
position: fixed;
top: 80px;
left: 50%;
transform: translateX(-50%);
background: #ffffff;
color: 000000;
padding: 12px 20px;
border-radius: 10px;
display: flex;
align-items: center;
gap: 10px;
z-index: 9999;
box-shadow: 0 5px 15px rgba(0,0,0,0.2);
border: 1px solid #eee;
">

<i class="bi bi-check-circle" style="font-size:20px; color:#22ce61;"></i>

<span>{{ session('success') }}</span>

<button type="button" onclick="this.parentElement.remove()" style="
border:none;
background:none;
font-size:18px;
margin-left:10px;
cursor:pointer;
">
×
</button>

</div>
@endif

<h4 class="mt-4" style="color:#000000; font-size:24px;">
<b>Manajemen User</b>
</h4>

<p style="color:#000000;">
Kelola akun pengguna sistem Permit to Work
</p>

<div class="card-box">

<div class="d-flex justify-content-between mb-3">

<div class="search-box">

<label>Cari User:</label>
<input type="text" id="searchUser" class="form-control" placeholder="Nama / Email">

<button class="btn btn-light">
<i class="bi bi-search"></i>
</button>

<label>Filter Role:</label>

<select id="filterRole" class="form-select" style="width:150px">
<option value="">Semua Role</option>
<option value="Pekerja">Worker</option>
<option value="Supervisor">Supervisor</option>
<option value="Safety Officer">Safety Officer</option>
<option value="Admin">Admin</option>
</select>

</div>

<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahUser">
<i class="bi bi-plus"></i> Tambah User
</button>

</div>

<table class="table table-bordered" id="userTable">

<thead>
<tr>
<th>No</th>
<th>Nama</th>
<th>Username</th>
<th>Departemen</th>
<th>Role</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
@foreach($users as $key => $user)

<tr>
<td>{{ $key+1 }}</td>
<td>{{ $user->name }}</td>
<td>{{ $user->username }}</td>
<td>
    {{ $user->department ?? '-' }}

    @if($user->sub_department)
        <span style="color:gray;"> / {{ $user->sub_department }}</span>
    @endif
</td>
<td class="role">{{ $user->role }}</td>

<td>
@if($user->status == 'Aktif')
<span class="badge-aktif">Aktif</span>
@else
<span class="badge-nonaktif">Nonaktif</span>
@endif
</td>

<td>
<button class="btn btn-sm btn-edit"
data-bs-toggle="modal"
data-bs-target="#editUser{{ $user->id }}">
Edit
</button>

<button class="btn btn-sm btn-hapus"
data-bs-toggle="modal"
data-bs-target="#hapusUser{{ $user->id }}">
Hapus
</button>
</td>

</tr>

@endforeach
</tbody>

</table>

</div>

</div>

<!-- MODAL EDIT USER (PER USER) -->
@foreach($users as $user)

<div class="modal fade" id="editUser{{ $user->id }}" tabindex="-1">
<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
<div class="modal-content custom-modal-content">

<form action="/admin/users/update/{{ $user->id }}" method="POST">
@csrf

<div class="modal-header border-0 pb-2">
<h5 class="fw-semibold">Edit User</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<div class="form-box">

<label>Nama Lengkap</label>
<div class="input-icon">
<i class="bi bi-person"></i>
<input type="text" name="name" placeholder="Nama Lengkap">
</div>

<label>Username</label>
<div class="input-icon">
<i class="bi bi-person-badge"></i>
<input type="text" name="username" placeholder="Username" required>
</div>

<label>Email</label>
<div class="input-icon">
<i class="bi bi-envelope"></i>
<input type="email" name="email" placeholder="email@outlook.com" required>
</div>

<label>Departemen</label>
<select name="department" class="form-select" onchange="handleDeptEdit(this.value, {{ $user->id }})">
    <option value="">Pilih Departemen</option>
    <option value="HR & IS Dept" {{ $user->department=='HR & IS Dept'?'selected':'' }}>HR & IS Dept</option>
    <option value="IHSE Dept" {{ $user->department=='IHSE Dept'?'selected':'' }}>IHSE Dept</option>
    <option value="Logistics Dept" {{ $user->department=='Logistics Dept'?'selected':'' }}>Logistics Dept</option>
    <option value="GA & ER Dept" {{ $user->department=='GA & ER Dept'?'selected':'' }}>GA & ER Dept</option>
    <option value="Maintenance Dept" {{ $user->department=='Maintenance Dept'?'selected':'' }}>Maintenance Dept</option>
    <option value="Operation Dept" {{ $user->department=='Operation Dept'?'selected':'' }}>Operation Dept</option>
    <option value="Technical Dept" {{ $user->department=='Technical Dept'?'selected':'' }}>Technical Dept</option>
</select>

<div id="subDeptBoxEdit{{ $user->id }}">
    <label>Sub Departemen</label>
    <select name="sub_department" id="subDeptEdit{{ $user->id }}" class="form-select" required>
        <option value="{{ $user->sub_department }}">{{ $user->sub_department }}</option>
    </select>
</div>

<label>Role</label>
<select name="role" class="form-select">
<option value="Admin" @selected($user->role=='Admin')>Admin</option>
<option value="Pekerja" @selected($user->role=='Worker')>Worker</option>
<option value="Supervisor" @selected($user->role=='Supervisor')>Supervisor</option>
<option value="Safety Officer" @selected($user->role=='Safety Officer')>Safety Officer</option>
</select>

<label>Status</label>
<select name="status" class="form-select">
<option value="Aktif" @selected($user->status=='Aktif')>Aktif</option>
<option value="Nonaktif" @selected($user->status=='Nonaktif')>Nonaktif</option>
</select>

</div>
</div>

<div class="modal-footer border-0 custom-footer">
<button type="button" class="btn btn-batal" data-bs-dismiss="modal">Batal</button>
<button type="submit" class="btn btn-update">Update</button>
</div>

</form>

</div>
</div>
</div>

@endforeach

<!-- MODAL HAPUS USER (PISAH!) -->
@foreach($users as $user)
<div class="modal fade" id="hapusUser{{ $user->id }}" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content text-center p-4" style="border-radius:16px">

<div style="font-size:60px; color:#f87171;">
<i class="bi bi-exclamation-circle"></i>
</div>

<h5 class="mt-3">Yakin Hapus User?</h5>

<div class="d-flex justify-content-center gap-3 mt-4">

<button class="btn btn-primary px-4" data-bs-dismiss="modal">
Batal
</button>

<form action="/admin/users/delete/{{ $user->id }}" method="POST">
@csrf
@method('DELETE')

<button type="submit" class="btn btn-danger px-4">
Hapus
</button>
</form>

</div>

</div>
</div>
</div>
@endforeach

<!-- MODAL TAMBAH USER -->
<div class="modal fade" id="tambahUser">
<div class="modal-dialog modal-dialog-centered modal-md">
<div class="modal-content custom-modal-content">

<form action="/admin/users/store" method="POST">
@csrf

<div class="modal-header border-0 pb-1">
<h5 class="fw-semibold">Tambah User</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body pt-1">

<div class="form-box">

<label>Nama Lengkap</label>
<div class="input-icon">
<i class="bi bi-person"></i>
<input type="text" name="name" placeholder="Nama Lengkap">
</div>

<label>Username</label>
<div class="input-icon">
<i class="bi bi-person-badge"></i>
<input type="text" name="username" placeholder="Username" required>
</div>

<label>Email</label>
<div class="input-icon">
<i class="bi bi-envelope"></i>
<input type="email" name="email" placeholder="email@outlook.com" required>
</div>

<label>Password</label>
<div class="input-icon">
<i class="bi bi-lock"></i>
<input type="password" name="password" placeholder="Password">
</div>

<label>Departemen</label>
<select name="department" class="form-select" onchange="handleDept(this.value)">
    <option value="">Pilih Departemen</option>
    <option value="HR & IS Dept">HR & IS Dept</option>
    <option value="IHSE Dept">IHSE Dept</option>
    <option value="Logistics Dept">Logistics Dept</option>
    <option value="GA & ER Dept">GA & ER Dept</option>
    <option value="Maintenance Dept">Maintenance Dept</option>
    <option value="Operation Dept">Operation Dept</option>
    <option value="Technical Dept">Technical Dept</option>
</select>

<div id="subDeptBoxTambah" style="display:none;">
    <label>Sub Departemen</label>
    <select name="sub_department" id="subDeptSelectTambah" class="form-select">
        <option value="">Pilih Sub</option>
    </select>
</div>

<label>Role</label>
<select name="role">
<option>Admin</option>
<option>Worker</option>
<option>Supervisor</option>
<option>Safety Officer</option>
</select>

<label>Status</label>
<select name="status">
<option>Aktif</option>
<option>Nonaktif</option>
</select>

</div>

</div>

<div class="modal-footer border-0 pt-2 custom-footer">

<button type="button" class="btn btn-batal" data-bs-dismiss="modal">
Batal
</button>

<button type="submit" class="btn btn-update">
Tambah User
</button>

</div>

</form>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

let searchInput = document.getElementById("searchUser");
let filterRole = document.getElementById("filterRole");

searchInput.addEventListener("keyup", filterUser);
filterRole.addEventListener("change", filterUser);

function filterUser(){

let keyword = searchInput.value.toLowerCase();
let role = filterRole.value;

let rows = document.querySelectorAll("#userTable tbody tr");

rows.forEach(function(row){

let nama = row.children[1].innerText.toLowerCase();
let username = row.children[2].innerText.toLowerCase();
let roleUser = row.children[4].innerText.trim().toLowerCase();
let roleFilter = role.toLowerCase();

let cocokSearch = nama.includes(keyword) || username.includes(keyword);
let cocokRole = role === "" || roleUser === roleFilter;

if(cocokSearch && cocokRole){
row.style.display="";
}else{
row.style.display="none";
}

});

}

</script>

<!-- AUTO HILANG 4 DETIK -->
<script>
setTimeout(function(){
    let notif = document.getElementById("notifSuccess");
    if(notif){
        notif.style.transition = "0.5s";
        notif.style.opacity = "0";
        setTimeout(() => notif.remove(), 500);
    }
}, 4000);
</script>

<script>
function handleDept(value){

    let subBox = document.getElementById("subDeptBoxTambah");
    let subSelect = document.getElementById("subDeptSelectTambah");

    subSelect.innerHTML = '<option value="">Pilih Sub</option>';

    let dataSub = {
        "HR & IS Dept": ["HR","DT","Accounting","Security","IT","IMS"],
        "IHSE Dept": ["Safety","Environment","Health"],
        "Logistics Dept": ["Warehouse","Transport","Inventory"],
        "GA & ER Dept": ["General Affair","External Relation"],
        "Maintenance Dept": ["Mechanical","Electrical"],
        "Operation Dept": ["Production","Operator"],
        "Technical Dept": ["Engineering","Project"]
    };

    if(dataSub[value]){
        subBox.style.display = "block";

        dataSub[value].forEach(function(item){
            let option = document.createElement("option");
            option.value = item;
            option.text = item;
            subSelect.appendChild(option);
        });

    } else {
        subBox.style.display = "none";
    }

}
</script>


<!-- 🔥 TAMBAHKAN DI SINI -->
<script>
function handleDeptEdit(dept, id){

    let subSelect = document.getElementById("subDeptEdit"+id);

    subSelect.innerHTML = '<option value="">Pilih Sub</option>';

    let dataSub = {
        "HR & IS Dept": ["HR","DT","Accounting","Security","IT","IMS"],
        "IHSE Dept": ["Safety","Environment","Health"],
        "Logistics Dept": ["Warehouse","Transport","Inventory"],
        "GA & ER Dept": ["General Affair","External Relation"],
        "Maintenance Dept": ["Mechanical","Electrical"],
        "Operation Dept": ["Production","Operator"],
        "Technical Dept": ["Engineering","Project"]
    };

    if(dataSub[dept]){
        dataSub[dept].forEach(function(item){
            let option = document.createElement("option");
            option.value = item;
            option.text = item;
            subSelect.appendChild(option);
        });
    }
}
</script>

<script>
document.querySelectorAll('[id^="editUser"]').forEach(function(modal){

    modal.addEventListener('shown.bs.modal', function(){

        let id = this.id.replace('editUser','');
        let deptSelect = this.querySelector('select[name="department"]');

        if(deptSelect){
            handleDeptEdit(deptSelect.value, id);
        }

    });

});
</script>

</body>
</html>