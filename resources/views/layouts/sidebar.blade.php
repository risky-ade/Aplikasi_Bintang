<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/dashboard" class="brand-link">
        <img src="{{ asset('img/logo.jpg') }}" alt="B4Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Bintang Empat</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('template/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="/" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cubes"></i>
                        <p>
                            Master
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/master_produk" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Produk</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/categories" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>Kategori</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/units" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>Satuan</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="/sales" class="nav-link">
                        <i class="mx-1 fas fa-solid fa-receipt"></i>
                        <p class="mx-2">
                            Penjualan Produk
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/sales/sales_invoices" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Faktur Penjualan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/sales/sales_retur" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>Retur Penjualan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/sales/sales_histories" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>Histori Harga Penjualan</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-regular fa-cart-arrow-down"></i>
                        <p class="mx-2">Pembelian Produk</p>
                        <i class="fas fa-angle-left right"></i>

                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/purchases/purchase_inv" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Faktur Pembelian</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/purchases_invoice" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>Retur Pembelian</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/purchases_histories" class="nav-link">
                                <i class="nav-icon fas fa-copy"></i>
                                <p>Histori Harga Pembelian</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-regular fa-folder-open"></i>
                        <p class="mx-2">Laporan</p>
                        <i class="fas fa-angle-left right"></i>

                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/reports/sales_report" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Laporan Penjualan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/purchases_report" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Laporan Pembelian</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-solid fa-user-plus"></i>
                        <p class="mx-2">Daftar Pihak</p>
                        <i class="fas fa-angle-left right"></i>

                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/customers" class="nav-link">
                                <i class="nav-icon fas fa-solid fa-users"></i>
                                <p>Daftar Pelanggan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/suppliers" class="nav-link">
                                <i class="nav-icon fas fa-solid fa-users"></i>
                                <p>Daftar Pemasok</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">Lainnya</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-solid fa-microchip"></i>
                        <p>
                            Pengaturan
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/users" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pengguna</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/profile" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Profil Perusahaan</p>
                            </a>
                        </li>
                </li>
            </ul>

        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
