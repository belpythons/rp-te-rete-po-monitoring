<!DOCTYPE html>
<html>
<head>

<title>Edit Profile - PT KMI</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
font-family:'Segoe UI', sans-serif;
background:#eef1f5;
}

.container{
max-width:600px;
margin-top:50px;
}

.card{
border-radius:15px;
padding:25px;
}
</style>

</head>

<body>

<div class="container">

<h4>Edit Profile</h4>

<div class="card">

<form method="POST" action="{{ route('admin.updateProfile') }}" enctype="multipart/form-data">
@csrf

<div class="mb-3">
<label>Nama</label>
<input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}">
</div>

<div class="mb-3">
<label>Username</label>
<input type="text" name="username" class="form-control" value="{{ auth()->user()->username }}">
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}">
</div>

<div class="mb-3">
<label>Foto</label>
<input type="file" name="foto" class="form-control">
</div>

<div class="mt-3 d-flex justify-content-between">

<a href="{{ route('admin.profile') }}" class="btn btn-secondary">
    Kembali
</a>

<button type="submit" class="btn btn-success">
    Simpan Perubahan
</button>

</div>

</form>

</div>

</div>

</body>
</html>