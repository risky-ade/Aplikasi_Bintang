<!DOCTYPE html>
<html lang="en">
@php
    use App\Helpers\Helper;
    use Illuminate\Support\Str;
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplikasi Bintang</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.jpg') }}">

   
<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{ asset('template/plugins/fontawesome-free/css/all.min.css') }}">
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- Tempusdominus Bootstrap 4 -->
{{-- <link rel="stylesheet" href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}"> --}}
<!-- iCheck -->
{{-- <link rel="stylesheet" href="{{ asset('template/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}"> --}}
<!-- JQVMap -->
<link rel="stylesheet" href="{{ asset('template/plugins/jqvmap/jqvmap.min.css') }}">
<!-- Theme style -->
{{-- <link rel="stylesheet" href="{{ asset('template/dist/css/adminlte.min.css') }}"> --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

<!-- DataTables Bootstrap 4 CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css"> --}}
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css"> --}}
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap4.css"> --}}
<!-- SweetAlert2 -->
<link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
<!-- Toastr -->
<link rel="stylesheet" href="{{ asset('template/plugins/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/OverlayScrollbars.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/Chart.min.css') }}">
<link rel="stylesheet" href="{{ asset('template/plugins/select2/css/select2.min.css') }}">
<script src="{{ asset('js/jquery.min.js') }}"></script>
     <style>
    .select2-container {
        width: 100% !important;
    }

    .select2-selection--single {
        height: 38px !important; 
    }

    .select2-selection__rendered {
        line-height: 38px !important;
    }

    .select2-selection__arrow {
        height: 38px !important;
    }
</style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
  {{-- <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="img/logo.jpg" alt="B4Logo" height="80" width="80">
  </div> --}}

    @include('layouts.navbar')

    <!-- Main Sidebar Container -->
    @include('layouts.sidebar')

    <main>
        @yield('content')
    </main>
    @include('layouts.footer')

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- <script src="{{ asset('js/jquery.min.js') }}"></script> --}}
{{-- <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script> --}}
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<!-- DataTables Bootstrap 4 JS -->
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('js/Chart.min.js') }}"></script>
<script src="{{ asset('js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('template/plugins/select2/js/select2.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('js/toastr.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('js/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/adminlte.js') }}"></script>
<script src="{{ asset('js/pages/dashboard.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('js/sparkline.js') }}"></script>
<!-- JQVMap -->
<script src="{{ asset('template/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('template/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('template/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('template/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('template/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('template/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('template/plugins/summernote/summernote-bs4.min.js') }}"></script>

{{-- <script>
    $(function() {
        /* ChartJS
            * -------
            * Here we will create a few charts using ChartJS
            */

        //-------------
        //- BAR CHART -
        //-------------
        var barChartCanvas = $('#barChart').get(0).getContext('2d')
        var barChartData = $.extend(true, {}, areaChartData)
        var temp0 = areaChartData.datasets[0]
        var temp1 = areaChartData.datasets[1]
        barChartData.datasets[0] = temp1
        barChartData.datasets[1] = temp0

        var barChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            datasetFill: false
        }

        new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions
        })

    })
    
</script> --}}
<!-- DataTables  & Plugins -->
<script src="{{ asset('template/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('template/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('template/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('template/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('template/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('template/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('template/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('template/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('template/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
        
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
<script>
    @if (session('success'))
        Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 2000
        });
    @endif

    @if (session('error'))
        Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '{{ session('error') }}'
        });
    @endif
</script>
</body>

</html>
