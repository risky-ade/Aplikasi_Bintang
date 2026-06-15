@extends('layouts.main')
@section('content')
@php
    $groupDefinitions = [
        'Master' => [
            'icon' => 'fas fa-cubes',
            'permissions' => [
                'master_produk.show',
                'master_produk.index',
                'master_produk.create',
                'master_produk.edit',
                'master_produk.store',
                'master_produk.update',
                'master_produk.delete',
                'master_produk.destroy',
                'master_produk.toggle',
                'produk.create',
                'produk.store',
                'produk.edit',
                'produk.update',
                'produk.destroy',
                'categories.index',
                'categories.create',
                'categories.store',
                'categories.edit',
                'categories.update',
                'categories.destroy',
                'kategori.destroy',
                'units.index',
                'units.create',
                'units.store',
                'units.edit',
                'units.update',
                'units.destroy',
                'stock_opname.index',
                'stock_opname.create',
                'stock_opname.store',
                'stock_opname.show',
                'stock_opname.edit',
                'stock_opname.update',
                'stock_opname.destroy',
                'stock_opname.approve',
            ],
        ],
        'Penjualan Produk' => [
            'icon' => 'fas fa-receipt',
            'permissions' => [
                'penjualan.index',
                'penjualan.show',
                'penjualan.create',
                'penjualan.store',
                'penjualan.edit',
                'penjualan.update',
                'penjualan.destroy',
                'penjualan.approve',
                'penjualan.unapprove',
                'penjualan.batal',
                'retur-penjualan.index',
                'retur-penjualan.show',
                'retur-penjualan.create',
                'retur-penjualan.store',
                'retur-penjualan.destroy',
                'histori-harga-jual.index',
                'histori-harga-jual.destroy-selected',
                'histori-harga-jual.destroy-by-date',
            ],
        ],
        'Pembelian Produk' => [
            'icon' => 'fas fa-cart-arrow-down',
            'permissions' => [
                'pembelian.index',
                'pembelian.show',
                'pembelian.create',
                'pembelian.store',
                'pembelian.edit',
                'pembelian.update',
                'pembelian.destroy',
                'pembelian.approve',
                'pembelian.unapprove',
                'pembelian.batal',
                'retur-pembelian.index',
                'retur-pembelian.show',
                'retur-pembelian.create',
                'retur-pembelian.store',
                'retur-pembelian.destroy',
                'histori-harga-beli.index',
                'histori-harga-beli.destroy-selected',
                'histori-harga-beli.destroy-by-date',
            ],
        ],
        'Laporan' => [
            'icon' => 'fas fa-folder-open',
            'permissions' => [
                'sales_report.index',
                'sales_report.sales_pdf',
                'purchases_report.index',
                'purchase_report.purchases_pdf',
                'profit_loss.index',
                'operational_expenses.index',
                'operational_expenses.create',
                'operational_expenses.store',
                'operational_expenses.edit',
                'operational_expenses.update',
                'operational_expenses.destroy',
            ],
        ],
        'Daftar Pihak' => [
            'icon' => 'fas fa-users',
            'permissions' => [
                'customers.index',
                'customers.show',
                'customers.create',
                'customers.store',
                'customers.edit',
                'customers.update',
                'customers.destroy',
                'suppliers.index',
                'suppliers.show',
                'suppliers.create',
                'suppliers.store',
                'suppliers.edit',
                'suppliers.update',
                'suppliers.destroy',
            ],
        ],
        'Lainnya' => [
            'icon' => 'fas fa-microchip',
            'permissions' => [
                'users.index',
                'users.create',
                'users.store',
                'users.edit',
                'users.update',
                'users.destroy',
                'roles.index',
                'roles.edit',
                'roles.update',
                'profiles',
                'profil.edit',
                'profil.update',
                'backup',
                'backup.index',
                'backup.run',
                'backup.download',
                'backup.destroy',
                'logs.index',
            ],
        ],
    ];

    $permissionsByName = $permissions->keyBy('name');
    $usedPermissionNames = collect();
    $groupedPermissions = collect($groupDefinitions)->map(function ($group) use ($permissionsByName, &$usedPermissionNames) {
        $items = collect($group['permissions'])
            ->map(fn ($name) => $permissionsByName->get($name))
            ->filter()
            ->values();

        $usedPermissionNames = $usedPermissionNames->merge($items->pluck('name'));

        return [
            'icon' => $group['icon'],
            'items' => $items,
        ];
    })->filter(fn ($group) => $group['items']->isNotEmpty());

    $otherPermissions = $permissions
        ->reject(fn ($permission) => $usedPermissionNames->contains($permission->name))
        ->values();

    if ($otherPermissions->isNotEmpty()) {
        $groupedPermissions->put('Permission Lainnya', [
            'icon' => 'fas fa-ellipsis-h',
            'items' => $otherPermissions,
        ]);
    }

    $isSuperadmin = $role->name === 'superadmin';
@endphp
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Atur Hak Akses</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Role</a></li>
            <li class="breadcrumb-item active">{{ $role->label ?? $role->name }}</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

      @if($isSuperadmin)
        <div class="alert alert-info">
          Role Superadmin memiliki akses penuh dan permission-nya tidak dapat diubah.
        </div>
      @endif

      <form method="POST" action="{{ route('roles.update', $role->id) }}">
        @csrf
        @method('PUT')

        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div>
              <strong>{{ $role->label ?? $role->name }}</strong>
              <div class="text-muted small">Pilih hak akses berdasarkan grup menu aplikasi.</div>
            </div>
            <div class="ml-auto">
              <button type="button" class="btn btn-outline-primary btn-sm" id="check-all-permissions" {{ $isSuperadmin ? 'disabled' : '' }}>
                Pilih Semua
              </button>
              <button type="button" class="btn btn-outline-secondary btn-sm" id="uncheck-all-permissions" {{ $isSuperadmin ? 'disabled' : '' }}>
                Kosongkan
              </button>
            </div>
          </div>

          <div class="card-body">
            <div class="row">
              @foreach($groupedPermissions as $groupName => $group)
                @php
                    $groupId = 'group_' . \Illuminate\Support\Str::slug($groupName, '_');
                    $checkedCount = $group['items']->filter(fn ($perm) => in_array($perm->id, $rolePermIds))->count();
                    $totalCount = $group['items']->count();
                @endphp
                <div class="col-lg-6 mb-3">
                  <div class="card h-100">
                    <div class="card-header bg-light">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <i class="{{ $group['icon'] }} mr-1"></i>
                          <strong>{{ $groupName }}</strong>
                          <span class="badge badge-secondary ml-1 group-counter" data-group="{{ $groupId }}">
                            {{ $checkedCount }}/{{ $totalCount }}
                          </span>
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-primary btn-check-group" data-target="{{ $groupId }}" {{ $isSuperadmin ? 'disabled' : '' }}>
                          Pilih Grup
                        </button>
                      </div>
                    </div>
                    <div class="card-body py-2 permission-group" id="{{ $groupId }}">
                      @foreach($group['items'] as $perm)
                        <div class="custom-control custom-checkbox py-1">
                          <input class="custom-control-input permission-checkbox"
                                 type="checkbox"
                                 name="permissions[]"
                                 value="{{ $perm->id }}"
                                 id="perm_{{ $perm->id }}"
                                 data-group="{{ $groupId }}"
                                 {{ in_array($perm->id, $rolePermIds) ? 'checked' : '' }}
                                 {{ $isSuperadmin ? 'disabled' : '' }}>
                          <label class="custom-control-label" for="perm_{{ $perm->id }}">
                            <strong>{{ $perm->label ?? $perm->name }}</strong>
                            <span class="text-muted small d-block">{{ $perm->name }}</span>
                          </label>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <div class="card-footer text-right">
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary" {{ $isSuperadmin ? 'disabled' : '' }}>Simpan</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>

<script>
$(function () {
    function updateGroupCounter(groupId) {
        const boxes = $('.permission-checkbox[data-group="' + groupId + '"]');
        const checked = boxes.filter(':checked').length;
        $('.group-counter[data-group="' + groupId + '"]').text(checked + '/' + boxes.length);
    }

    function updateAllCounters() {
        $('.permission-group').each(function () {
            updateGroupCounter(this.id);
        });
    }

    $('.permission-checkbox').on('change', function () {
        updateGroupCounter($(this).data('group'));
    });

    $('.btn-check-group').on('click', function () {
        const groupId = $(this).data('target');
        const boxes = $('.permission-checkbox[data-group="' + groupId + '"]');
        const allChecked = boxes.length === boxes.filter(':checked').length;
        boxes.prop('checked', !allChecked);
        updateGroupCounter(groupId);
        $(this).text(allChecked ? 'Pilih Grup' : 'Kosongkan Grup');
    });

    $('#check-all-permissions').on('click', function () {
        $('.permission-checkbox').prop('checked', true);
        updateAllCounters();
    });

    $('#uncheck-all-permissions').on('click', function () {
        $('.permission-checkbox').prop('checked', false);
        updateAllCounters();
    });
});
</script>
@endsection
