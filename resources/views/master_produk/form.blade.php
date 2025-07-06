<div class="form-group">
    <label>Nama Produk</label>
    <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk', $masterProduk->nama_produk ?? '') }}" required>
</div>

<div class="form-group">
    <label>Kategori</label>
    <select name="kategori_id" class="form-control" required>
        @foreach($kategori as $k)
            <option value="{{ $k->id }}" {{ isset($masterProduk) && $masterProduk->kategori_id == $k->id ? 'selected' : '' }}>
                {{ $k->nama_kategori }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Satuan</label>
    <select name="satuan_id" class="form-control" required>
        @foreach($satuan as $s)
            <option value="{{ $s->id }}" {{ isset($masterProduk) && $masterProduk->satuan_id == $s->id ? 'selected' : '' }}>
                {{ $s->jenis_satuan }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Harga Dasar</label>
    <input type="number" name="harga_dasar" class="form-control" value="{{ old('harga_dasar', $masterProduk->harga_dasar ?? '') }}" required>
</div>
<div class="form-group">
    <label for="harga_jual">Harga Jual</label>
    <input type="number" class="form-control" value="{{ old('harga_dasar', $masterProduk->harga_jual ?? '') }}" name="harga_jual" id="harga_jual" required>
</div>
<div class="form-group">
    <label for="include_pajak">Harga Termasuk Pajak?</label>
    <select class="form-control" name="include_pajak" id="include_pajak">
        <option value="0">Tidak</option>
        <option value="1">Ya</option>
    </select>
</div>
<div class="form-group">
    <label>Stok</label>
    <input type="number" name="stok" class="form-control" value="{{ old('stok', $masterProduk->stok ?? '') }}" required>
</div>
<div class="form-group">
    <label for="stok_minimal">Stok Minimal (peringatan)</label>
    <input type="number" class="form-control" name="stok_minimal" id="stok_minimal" value="{{ old('stok', $masterProduk->stok_minimal ?? '') }}">
</div>
<div class="form-group">
    <label>Deskripsi</label>
    <textarea name="deskripsi" class="form-control">{{ old('deskripsi', $masterProduk->deskripsi ?? '') }}</textarea>
</div>

<div class="form-group">
    <label>Gambar Produk</label>
    <input type="file" name="gambar" class="form-control">
    @if(isset($masterProduk) && $masterProduk->gambar)
        <img src="{{ asset('storage/'.$masterProduk->gambar) }}" width="100" class="mt-2">
    @endif
</div>
