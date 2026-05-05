<!DOCTYPE html>
<html>
<head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Register - Permit To Work PT KMI</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

*{
box-sizing:border-box;
}

body{
margin:0;
font-family:Arial;
min-height:100vh;
background:#000;
display:flex;
flex-direction:column;
}

/* HEADER */

.top-header{
background:#445A82;
color:white;
padding:15px 40px;
display:flex;
align-items:center;
height:180px;

clip-path: polygon(0 0, 100% 0, 100% 60%, 0 100%);

z-index:10;
position:relative;
}

.top-header img{
width:220px;
margin-right:20px;
}

.top-header h2{
position:absolute;
left:50%;
transform:translateX(-50%);
margin:0;
font-weight:bold;
letter-spacing:1px;
font-size:40px;
}

/* MAIN */

.main-container{
flex:1;
display:flex;
margin-top:-80px;
flex-wrap:wrap;
position:relative;
}

/* BACKGROUND */

.left-side{
position:absolute;
top:0;
left:0;
width:100%;
height:100%;
z-index:1;
background-image:url("{{ asset('images/kmi1.jpg') }}");
background-size:cover;
background-position:75% center;
background-repeat:no-repeat;

/* 🔥 INI YANG DITAMBAH */
transform: scaleX(-1);
}

.left-side::after{
content:"";
position:absolute;
width:100%;
height:100%;
background:rgba(0,0,0,0.25);
}

/* REGISTER AREA */

.right-side{
width:420px;
max-width:100%;
display:flex;
align-items:center;
justify-content:center;
padding:30px;

position:absolute;
top:50%;
right:5%;
transform:translateY(-50%);
z-index:5;
}

/* CARD */

.register-card{
background:rgba(255,255,255,0.15);
backdrop-filter:blur(12px);
padding:40px;
border-radius:12px;
width:100%;
max-width:340px;
box-shadow:0 10px 30px rgba(0,0,0,0.3);
}

/* TABS */

.register-tabs{
display:flex;
border-bottom:2px solid rgba(255,255,255,0.3);
margin-bottom:20px;
}

.register-tabs div{
margin-right:20px;
padding-bottom:10px;
cursor:pointer;
font-weight:bold;
color:#ddd;
}

.register-tabs .active{
color:#2d6cdf;
border-bottom:3px solid #2d6cdf;
}

/* INPUT */

.form-group{
margin-bottom:20px;
}

.input-icon{
position:relative;
}

.input-icon i{
position:absolute;
left:10px;
top:50%;
transform:translateY(-50%);
color:#555;
}

.input-icon input{
padding-left:35px;
border-radius:6px;
}

/* BUTTON */

.register-btn{
width:100%;
background:#2d6cdf;
border:none;
padding:12px;
color:white;
font-weight:bold;
border-radius:6px;
}

.register-btn:hover{
background:#1d4fa8;
}

/* RESPONSIVE */

@media (max-width:992px){

.top-header h2{
font-size:28px;
}

.top-header img{
width:180px;
}

.right-side{
right:50%;
transform:translate(50%,-50%);
}

}

@media (max-width:768px){

.top-header{
height:140px;
padding:10px 20px;
}

.top-header img{
width:150px;
}

.top-header h2{
font-size:22px;
}

.main-container{
margin-top:-40px;
flex-direction:column;
}

.left-side{
position:relative;
height:260px;
background:
url("{{ asset('images/pabrik1.JPG') }}") center/contain no-repeat;
}

.right-side{
position:relative;
top:auto;
right:auto;
transform:none;
width:100%;
padding:20px;
}

.register-card{
margin-top:-60px;
}

}

@media (max-width:480px){

.top-header img{
width:120px;
}

.top-header h2{
font-size:18px;
}

.register-card{
padding:25px;
}

}

</style>

</head>

<body>

<!-- HEADER -->

<div class="top-header">
<img src="{{ asset('images/kmi-logo.png') }}">
<h2>PERMIT TO WORK - PT.KMI</h2>
</div>

<div class="main-container">

<!-- BACKGROUND -->
<div class="left-side"></div>

<!-- REGISTER -->
<div class="right-side">

<div class="register-card">

<div class="register-tabs">
<div onclick="window.location='/login'">Login</div>
<div class="active">Sign Up</div>
</div>

@if ($errors->any())
<div class="alert alert-danger">
<ul class="mb-0">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<form method="POST" action="/register">
@csrf

<input type="hidden" name="role" value="pekerja">

<div class="form-group">
<div class="input-icon">
<i class="bi bi-person"></i>
<input type="text" name="name" class="form-control" placeholder="Full Name" required>
</div>
</div>

<div class="form-group">
<div class="input-icon">
<i class="bi bi-envelope"></i>
<input type="email" name="email" class="form-control" placeholder="Email" required>
</div>
</div>

<div class="form-group">
<div class="input-icon">
<i class="bi bi-lock"></i>
<input type="password" name="password" class="form-control" placeholder="Password" required>
</div>
</div>

<div class="form-group">
<div class="input-icon">
<i class="bi bi-lock-fill"></i>
<input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
</div>
</div>

<button type="submit" class="register-btn">
Register
</button>

</form>

</div>

</div>

</div>

</body>
</html>