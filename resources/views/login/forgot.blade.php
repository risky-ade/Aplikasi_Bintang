<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lupa Password</title>
  <link rel="stylesheet" href="template/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="template/plugins/fontawesome-free/css/all.min.css">
</head>
<body class="hold-transition login-page bg-secondary">

<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <b>Lupa Password</b>
    </div>
    <div class="card-body">

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('password.request.store') }}" method="post">
        @csrf

        <div class="input-group mb-3">
          <input type="text" name="login" class="form-control" placeholder="Masukkan Email" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-user"></span></div>
          </div>
        </div>

        <div class="form-group">
          <textarea name="note" class="form-control" rows="2" placeholder="Catatan (opsional)"></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Kirim ke Admin</button>
        <a href="/login" class="btn btn-link btn-block">Kembali ke Login</a>
      </form>

    </div>
  </div>
</div>

</body>
</html>