@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">Edit Biaya Operasional</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('operational_expenses.update', $operationalExpense->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $operationalExpense->tanggal->format('Y-m-d')) }}" required>
              </div>
              <div class="col-md-3">
                <label>Kategori</label>
                <input type="text" name="kategori" class="form-control" value="{{ old('kategori', $operationalExpense->kategori) }}" required>
              </div>
              <div class="col-md-3">
                <label>Nominal</label>
                <input type="number" name="nominal" class="form-control text-right" value="{{ old('nominal', $operationalExpense->nominal) }}" min="0" step="0.01" required>
              </div>
              <div class="col-md-3">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan', $operationalExpense->keterangan) }}">
              </div>
            </div>
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('operational_expenses.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
            <button type="submit" class="btn btn-primary btn-sm">Update</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>
@endsection
