<!DOCTYPE html>
<html>
<head>

<title>Permit To Work - PT.KMI</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<style>

body{
font-family:Segoe UI;
margin:0;

/* 🔥 BACKGROUND GAMBAR (FIX) */
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
position:relative; /* 🔥 FIX */
z-index:2;          /* 🔥 FIX */
}

/* TOPBAR */

.topbar{
background: rgba(22, 61, 87, 0.9);
backdrop-filter: blur(6px);
color:white;
padding:12px 25px;
border-radius:30px;
display:flex;
justify-content:space-between;
align-items:center;
}

.user-box{
display:flex;
align-items:center;
gap:12px;
}

.user-box img{
width:35px;
height:35px;
border-radius:50%;
}

/* STAT CARD */

.card-stat{
border-radius:18px;
color:white;
padding:20px;
display:flex;
align-items:center;
gap:15px;
cursor:pointer;
transition:0.3s;
}

.card-stat:hover{
transform:scale(1.05);
}

.card-active{
outline:3px solid #00000030;
}

.card-stat i{
font-size:28px;
}

.blue{background:#4e79c7;}
.orange{background:#f39c38;}
.green{background:#5aa469;}
.red{background:#d64545;}
.cyan{background:#6fa9bd;}

.card-stat h4{
margin:0;
font-weight:700;
}

/* TABLE CARD */

.card-box{
background: rgba(255,255,255,0.95); /* 🔥 FIX TRANSPARAN */
backdrop-filter: blur(6px);
border-radius:18px;
padding:22px;
margin-top:20px;
box-shadow:0 3px 8px rgba(0,0,0,0.05);
}

.table th{
font-weight:600;
color:#333;
}

.table td{
vertical-align:middle;
}

/* STATUS */

.status{
display:inline-block;
width:100px;
text-align:center;
padding:6px 0;
border-radius:20px;
font-size:13px;
font-weight:600;
}

.pending{background:#f39c38;color:white;}
.disetujui{background:#5aa469;color:white;}
.ditolak{background:#d64545;color:white;}
.selesai{background:#6fa9bd;color:white;}

/* BUTTON */

.btn-detail{
background:#4e79c7;
color:white;
border-radius:8px;
padding:5px 12px;
font-size:13px;
}

/* CHART CARD */

/* 🔥 FIX: TIDAK DIHAPUS, HANYA DIPERBAIKI */
.chart-card{
background: rgba(255,255,255,0.95);
backdrop-filter: blur(6px);
border-radius:20px;
padding:22px;
height:280px;
box-shadow:0 3px 8px rgba(0,0,0,0.05);
}

.chart-container{
position:relative;
height:200px;
}

.chart-title{
font-weight:600;
margin-bottom:10px;
font-size:16px;
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

.profile-box{
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

</style>

</head>

<body>

<!-- 🔥 OVERLAY -->
<div class="overlay"></div>

<div class="sidebar">

<div class="sidebar-header">
<img src="/images/kmi-logo.png">
</div>

<a class="active" href="/admin/dashboard">
<i class="bi bi-speedometer2"></i> Dashboard
</a>

<a href="/admin/users">
<i class="bi bi-people"></i> Manajemen User
</a>

<a href="{{ route('admin.monitoring') }}">
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
<button type="submit" class="btn btn-danger w-100 mb-2">Logout</button>
</form>
<span style="font-size:14px;">Admin</span>
</div>

</div>

<div class="content">

<div class="topbar">
<b>PERMIT TO WORK - PT.KMI</b>
<div class="user-box position-relative">
    
<i class="bi bi-bell"></i>

<span>Admin</span>

<div class="profile-trigger" onclick="toggleProfile(event)" style="cursor:pointer;">
    <img src="/images/me.jpg">
</div>


<!-- DROPDOWN PROFIL -->
<div id="profileBox" class="profile-box d-none" onclick="event.stopPropagation()">

    <div class="profile-header">
        <img src="/images/me.jpg">
        <div class="profile-text">
            <b>ayuu03</b>
            <small>ayuu17@outlook.com</small>
        </div>
    </div>

    <a href="{{ route('admin.profile') }}" class="btn-edit">
    <i class="bi bi-pencil"></i> Edit Profil
    </a>

    <form method="POST" action="/logout">
        <button class="btn-logout">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>

</div>

</div>
</div>

<h4 class="mt-4" style="color:#000; font-size:24px;">
<b>Selamat Datang,</b> Admin
</h4>

<!-- STAT -->
<div class="row mt-3">

<div class="col-md-2">
<div class="card-stat blue" onclick="filterTable('all', this)">
<i class="bi bi-file-earmark-check"></i>
<div><div>Total Permit</div><h4>10</h4></div>
</div>
</div>

<div class="col-md-2">
<div class="card-stat orange" onclick="filterTable('Pending', this)">
<i class="bi bi-hourglass-split"></i>
<div><div>Permit Pending</div><h4>2</h4></div>
</div>
</div>

<div class="col-md-2">
<div class="card-stat green" onclick="filterTable('Disetujui', this)">
<i class="bi bi-check-circle"></i>
<div><div>Permit Disetujui</div><h4>1</h4></div>
</div>
</div>

<div class="col-md-3">
<div class="card-stat red" onclick="filterTable('Ditolak', this)">
<i class="bi bi-x-circle"></i>
<div><div>Permit Ditolak</div><h4>2</h4></div>
</div>
</div>

<div class="col-md-3">
<div class="card-stat cyan" onclick="filterTable('Selesai', this)">
<i class="bi bi-check2-all"></i>
<div><div>Permit Selesai</div><h4>5</h4></div>
</div>
</div>

</div>

<!-- TABLE -->
<div class="card-box">
<h5>Permit Terbaru</h5>

<table class="table mt-3" id="permitTable">
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
<td>PRM-010</td>
<td>Dinda Cahya</td>
<td>Storage 3</td>
<td>Cold Work</td>
<td>11 Apr 2026</td>
<td data-status="Pending"><span class="status pending">Pending</span></td>
<td><button class="btn-detail">Detail</button></td>
</tr>

<tr>
<td>PRM-009</td>
<td>Mira Agustiansyah</td>
<td>Storage 3</td>
<td>Cold Work</td>
<td>03 Apr 2026</td>
<td data-status="Ditolak"><span class="status ditolak">Ditolak</span></td>
<td><button class="btn-detail">Detail</button></td>
</tr>

<tr>
<td>PRM-008</td>
<td>Yoga Prasetya</td>
<td>Line 1</td>
<td>Hot Work</td>
<td>19 Mar 2026</td>
<td data-status="Pending"><span class="status pending">Pending</span></td>
<td><button class="btn-detail">Detail</button></td>
</tr>

<tr>
<td>PRM-007</td>
<td>Mira Agustiansyah</td>
<td>Area Las</td>
<td>Cold Work</td>
<td>06 Mar 2026</td>
<td data-status="Disetujui"><span class="status disetujui">Disetujui</span></td>
<td><button class="btn-detail">Detail</button></td>
</tr>

<tr>
<td>PRM-006</td>
<td>Fikri Ramadhan</td>
<td>Area Las</td>
<td>Cold Work</td>
<td>02 Mar 2026</td>
<td data-status="Selesai"><span class="status selesai">Selesai</span></td>
<td><button class="btn-detail">Detail</button></td>
</tr>

</tbody>
</table>
</div>

<!-- CHART -->
<div class="row mt-3">

<div class="col-md-6">
<div class="chart-card">
<div class="chart-title">Statistik Permit Bulanan</div>
<div class="chart-container">
<canvas id="chart1"></canvas>
</div>
</div>
</div>

<div class="col-md-6">
<div class="chart-card">
<div class="chart-title">Jenis Pekerjaan</div>
<div class="chart-container">
<canvas id="chart2" width="260" height="260"></canvas>
</div>
</div>
</div>

</div>

</div>

<script>

// ===============================
// AUTO HITUNG
// ===============================
function updateStat(){
let rows=document.querySelectorAll("#permitTable tbody tr");
let total=0,p=0,d=0,t=0,s=0;

rows.forEach(row=>{
let status=row.querySelector("td[data-status]").getAttribute("data-status");
total++;
if(status.toLowerCase()=="pending")p++;
if(status.toLowerCase()=="disetujui")d++;
if(status.toLowerCase()=="ditolak")t++;
if(status.toLowerCase()=="selesai")s++;
});

let card=document.querySelectorAll(".card-stat h4");
card[0].innerText=total;
card[1].innerText=p;
card[2].innerText=d;
card[3].innerText=t;
card[4].innerText=s;
}

// ===============================
// FILTER TABLE
// ===============================
function filterTable(status, el){

let rows=document.querySelectorAll("#permitTable tbody tr");

document.querySelectorAll(".card-stat").forEach(c=>c.classList.remove("card-active"));
if(el) el.classList.add("card-active");

rows.forEach(row=>{
let s=row.querySelector("td[data-status]").getAttribute("data-status");

if(status==="all" || s.toLowerCase()===status.toLowerCase()){
row.style.display="";
}else{
row.style.display="none";
}
});

// update chart
updateLineChart(status);
updateChart(status);
}

// ===============================
// PIE CHART FIXED
// ===============================
function updateChart(status){

let rows=document.querySelectorAll("#permitTable tbody tr");

let dataJenis={
    hot:0,
    cold:0,
    penggalian:0,
    listrik:0,
    kendaraan:0,
    confined:0,
    kompresor:0
};

// 🔥 RESET DULU
chart2.data.datasets[0].data = [0,0,0,0,0,0,0];

rows.forEach(row=>{
    let s=row.querySelector("td[data-status]").getAttribute("data-status");
    let jenis=row.children[3].innerText.toLowerCase();

    if(status==="all" || s.toLowerCase()===status.toLowerCase()){

        if(jenis.includes("hot")) dataJenis.hot++;
        else if(jenis.includes("cold")) dataJenis.cold++;
        else if(jenis.includes("penggalian")) dataJenis.penggalian++;
        else if(jenis.includes("listrik")) dataJenis.listrik++;
        else if(jenis.includes("kendaraan")) dataJenis.kendaraan++;
        else if(jenis.includes("confined")) dataJenis.confined++;
        else if(jenis.includes("kompresor")) dataJenis.kompresor++;
    }
});

chart2.data.datasets[0].data=[
    dataJenis.hot,
    dataJenis.cold,
    dataJenis.penggalian,
    dataJenis.listrik,
    dataJenis.kendaraan,
    dataJenis.confined,
    dataJenis.kompresor
];

chart2.update();
}

// ===============================
// LINE CHART DINAMIS
// ===============================
function updateLineChart(status){

let rows=document.querySelectorAll("#permitTable tbody tr");

let bulanMap={
"Jan":0,
"Feb":1,
"Mar":2,
"Apr":3
};

let dataStatus={
Disetujui:[0,0,0,0],
Pending:[0,0,0,0],
Ditolak:[0,0,0,0],
Selesai:[0,0,0,0]
};

rows.forEach(row=>{

let s=row.querySelector("td[data-status]").getAttribute("data-status");
let tanggal=row.children[4].innerText;
let bulan=tanggal.split(" ")[1];

let index=bulanMap[bulan];

if(index!==undefined){

if(status==="all"){
dataStatus[s][index]++;
}else if(s.toLowerCase()===status.toLowerCase()){
dataStatus[s][index]++;
}

}

});

chart1.data.datasets[0].data=dataStatus["Disetujui"];
chart1.data.datasets[1].data=dataStatus["Pending"];
chart1.data.datasets[2].data=dataStatus["Ditolak"];
chart1.data.datasets[3].data=dataStatus["Selesai"];

chart1.data.datasets.forEach(ds=>{
if(status==="all"){
ds.hidden=false;
}else{
ds.hidden = ds.label.toLowerCase() !== status.toLowerCase();
}
});

chart1.update();
}

// ===============================
// RUN INIT
// ===============================
updateStat();

// ===============================
// CHART STYLE
// ===============================
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.pointStyle = 'circle';

// ===============================
// LINE CHART
// ===============================
let chart1=new Chart(document.getElementById("chart1"),{
type:"line",
data:{
labels:["Jan","Feb","Mar","Apr"],
datasets:[

{
label:"Disetujui",
data:[0,0,0,0],
borderColor:"#5aa469",
backgroundColor:"#5aa469",
fill:false,
tension:0.4
},

{
label:"Pending",
data:[0,0,0,0],
borderColor:"#f39c38",
backgroundColor:"#f39c38",
fill:false,
tension:0.4
},

{
label:"Ditolak",
data:[0,0,0,0],
borderColor:"#d64545",
backgroundColor:"#d64545",
fill:false,
tension:0.4
},

{
label:"Selesai",
data:[0,0,0,0],
borderColor:"#6fa9bd",
backgroundColor:"#6fa9bd",
fill:false,
tension:0.4
}

]
},
options:{
responsive:true,
maintainAspectRatio:false,
plugins:{legend:{position:'bottom'}}
}
});

// ===============================
// PIE CHART (FIXED TOTAL)
// ===============================
let chart2 = new Chart(document.getElementById("chart2"), {
type: "pie",
data: {
labels: [
"Hot Work",
"Cold Work",
"Penggalian",
"Listrik & Instrument",
"Kendaraan & Alat Berat",
"Confined Space",
"Kompressor Oksigen"
],
datasets: [{
data: [3,4,0,1,0,2,0],
backgroundColor: [
"#e84c3d",
"#3498db",
"#e67e22",
"#f1c40f",
"#95a5a6",
"#9b59b6",
"#1abc9c"
],
borderWidth: 1
}]
},
options: {
responsive: true,
maintainAspectRatio: false,
plugins: {
legend: {
position: 'right',
labels: {
usePointStyle: true,
pointStyle: 'circle'
}
},
tooltip: {
callbacks: {
label: function(context) {
let value = context.raw;
let data = context.dataset.data;
let total = data.reduce((a,b)=>a+b,0);
let percentage = ((value / total) * 100).toFixed(0);
return context.label + " " + percentage + "%";
}
}
}
}
}
});

// ===============================
// INIT CHART
// ===============================

updateLineChart('all');

// ===============================
// PROFILE DROPDOWN
// ===============================
function toggleProfile(event){
event.stopPropagation();
let box = document.getElementById("profileBox");
box.classList.toggle("show");
box.classList.toggle("d-none");
}

document.addEventListener("click", function(e){
let trigger = document.querySelector(".profile-trigger");
let box = document.getElementById("profileBox");

if (!box.classList.contains("d-none")) {
if (!trigger.contains(e.target) && !box.contains(e.target)) {
box.classList.add("d-none");
}
}
});

</script>

</body>
</html>