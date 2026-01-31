@extends('layouts.main')

@section('content')
<div class="container-fluid">
  <h3>Error Log</h3>

  <form method="GET" class="mb-3">
    <select name="file" class="form-control w-25" onchange="this.form.submit()">
      <option value="laravel.log">Laravel</option>
      <option value="backup.log">Backup</option>
      <option value="auth.log">Auth</option>
    </select>
  </form>

  <pre style="background:#111;color:#0f0;height:600px;overflow:auto">
@foreach($logs as $log)
{{ $log }}
@endforeach
  </pre>
</div>
@endsection