<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>

      <!-- Notifikasi Stok Minimum -->
      <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
              <i class="far fa-bell"></i>
              @if ($produk_stok_minim->count() > 0)
                  <span class="badge badge-warning navbar-badge">{{ $produk_stok_minim->count() }}</span>
              @endif
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              <span class="dropdown-item dropdown-header">
                  {{ $produk_stok_minim->count() }} Notifikasi Stok Minimum
              </span>

              @foreach ($produk_stok_minim as $produk)
                  <div class="dropdown-divider"></div>
                  <a href="{{ route('master_produk.index') }}" class="dropdown-item">
                      <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                      Stok "{{ $produk->nama_produk }}" tersisa {{ $produk->stok }}
                  </a>
              @endforeach

              <div class="dropdown-divider"></div>
              <a href="{{ route('master_produk.index') }}" class="dropdown-item dropdown-footer">Lihat Semua Produk</a>
          </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <form action="/logout" method="post">
          @csrf
          <button type="submit" class="nav-link bg-transparent border-transparent">Logout <i class="fas fa-sign-out-alt"></i></button>
        </form>
        {{-- <a class="nav-link" href="#" role="button">
          Logout
          <i class="fas fa-sign-out-alt"></i>
        </a> --}}
      </li>
      {{-- <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li> --}}
    </ul>
  </nav>