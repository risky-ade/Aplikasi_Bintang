@csrf
<div class="row mb-3">
<div class="col-md-4">
    <label for="nama" class="form-label">Nama</label>
    <input type="text" name="nama" class="form-control" value="{{ old('nama', $pelanggan->nama ?? '') }}" required>
</div>

<div class="col-md-4">
    <label for="email" class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $pelanggan->email ?? '') }}">
</div>

<div class="col-md-4">
    <label for="npwp" class="form-label">NPWP</label>
    <input type="text" name="npwp" class="form-control" value="{{ old('npwp', $pelanggan->npwp ?? '') }}">
</div>
</div>
<div class="row mb-3">
<div class="col-md-4">
    <label for="no_hp" class="form-label">No HP</label>
    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $pelanggan->no_hp ?? '') }}">
</div>

<div class="col-md-4">
    <label for="kota" class="form-label">Kota</label>
    <input type="text" name="kota" class="form-control" value="{{ old('kota', $pelanggan->kota ?? '') }}">
</div>

<div class="col-md-4">
    <label for="provinsi" class="form-label">Provinsi</label>
    <input type="text" name="provinsi" class="form-control" value="{{ old('provinsi', $pelanggan->provinsi ?? '') }}">
</div>
</div>

<div class="mb-3">
    <label for="alamat" class="form-label">Alamat</label>
    <textarea name="alamat" class="form-control">{{ old('alamat', $pelanggan->alamat ?? '') }}</textarea>
</div>
<div class="row justify-content-end">
    <div class="mx-2">
        <button type="submit" class="btn btn-primary">{{ $submitText ?? 'Simpan' }}</button>
    </div>
    <a href="{{ route('customers.index') }}" class="btn btn-secondary">Kembali</a>
</div>
