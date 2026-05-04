@extends('layouts.main')
@section('content')
<style>
    thead tr {
        background-color: #001f3f;
        color: white;
    }

    .form-control {
        height: 36px;
        padding: 0.25rem 0.5rem;
    }

    .table td, .table th {
        vertical-align: middle;
    }

    .produk-column {
        min-width: 240px;
    }

    .number-input {
        text-align: right;
    }
</style>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Stok Opname</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Stok Opname</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container mt-4">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('stock_opname.store') }}" method="POST" id="form-opname">
                @csrf
                <div class="card">
                <div class="card-header">
                    <h3>Input Stock Opname</h3>
                </div>

                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>No Opname</label>
                            <input type="text" name="no_opname" class="form-control" value="{{ $no }}" readonly>
                        </div>

                        <div class="col-md-4">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label>Catatan</label>
                            <input type="text" name="catatan" class="form-control">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="table-opname">
                            <thead>
                                <tr>
                                    <th class="produk-column">Produk</th>
                                    <th>Stok Sistem</th>
                                    <th>Stok Fisik</th>
                                    <th>Selisih</th>
                                    <th>
                                        <button type="button" class="btn btn-sm btn-success" onclick="tambahBaris()">+</button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="produk-body">
                                <tr>
                                    <td>
                                        <select name="produk_id[]" class="form-control produk-select w-100" required></select>
                                    </td>
                                    <td>
                                        <input type="number" name="stok_sistem[]" class="form-control number-input stok-sistem" readonly required>
                                    </td>
                                    <td>
                                        <input type="number" name="stok_fisik[]" class="form-control number-input stok-fisik" min="0" required>
                                    </td>
                                    <td>
                                        <input type="number" name="selisih[]" class="form-control number-input selisih" readonly value="0">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">x</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('stock_opname.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </div>
                </form>
            </div>
        </section>
    </div>

<template id="produk-row-template">
    <tr>
        <td>
            <select name="produk_id[]" class="form-control produk-select w-100" required></select>
        </td>
        <td>
            <input type="number" name="stok_sistem[]" class="form-control number-input stok-sistem" readonly required>
        </td>
        <td>
            <input type="number" name="stok_fisik[]" class="form-control number-input stok-fisik" min="0" required>
        </td>
        <td>
            <input type="number" name="selisih[]" class="form-control number-input selisih" readonly value="0">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="hapusBaris(this)">x</button>
        </td>
    </tr>
</template>

<script>
function initSelect2() {
    $('.produk-select').select2({
        placeholder: 'Cari Produk...',
        width: '100%',
        ajax: {
            url: '{{ route("produk.search") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { term: params.term };
            },
            processResults: function(data) {
                return { results: data.results };
            }
        }
    }).off('select2:select').on('select2:select', function(e) {
        const data = e.params.data;
        const selectedId = String(data.id);
        const currentSelect = this;
        let duplicate = false;

        $('.produk-select').not(currentSelect).each(function() {
            if (String($(this).val()) === selectedId) {
                duplicate = true;
            }
        });

        if (duplicate) {
            alert('Produk sudah ada di tabel.');
            $(this).val(null).trigger('change');
            return;
        }

        const row = $(this).closest('tr');
        row.find('.stok-sistem').val(data.stok || 0);
        row.find('.stok-fisik').val(data.stok || 0).trigger('input');
    });
}

function tambahBaris() {
    const template = document.getElementById('produk-row-template').content.cloneNode(true);
    $('#produk-body').append(template);
    initSelect2();
}

function hapusBaris(btn) {
    if ($('#produk-body tr').length > 1) {
        $(btn).closest('tr').remove();
    }
}

function hitungSelisih(row) {
    const sistem = parseInt(row.find('.stok-sistem').val()) || 0;
    const fisik = parseInt(row.find('.stok-fisik').val()) || 0;
    const selisih = fisik - sistem;
    const input = row.find('.selisih');

    input.val(selisih);
    input.removeClass('text-danger text-success');

    if (selisih < 0) {
        input.addClass('text-danger');
    } else if (selisih > 0) {
        input.addClass('text-success');
    }
}

$(document).ready(function() {
    initSelect2();

    $(document).on('input', '.stok-fisik', function() {
        hitungSelisih($(this).closest('tr'));
    });

    $('#form-opname').on('submit', function(e) {
        if ($('#produk-body tr').length < 1 || $('.produk-select').filter(function() { return $(this).val(); }).length < 1) {
            e.preventDefault();
            alert('Pilih minimal satu produk untuk stock opname.');
        }
    });
});
</script>
@endsection
