<?php

if (!function_exists('rupiah')) {
    function rupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('terbilang')) {
    function terbilang($angka)
    {
        $angka = abs($angka);
        $bilangan = [
            "", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"
        ];

        if ($angka < 12) {
            return $bilangan[$angka];
        } elseif ($angka < 20) {
            return terbilang($angka - 10) . " belas";
        } elseif ($angka < 100) {
            return terbilang($angka / 10) . " puluh " . terbilang($angka % 10);
        } elseif ($angka < 200) {
            return "seratus " . terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return terbilang($angka / 100) . " ratus " . terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return "seribu " . terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return terbilang($angka / 1000) . " ribu " . terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return terbilang($angka / 1000000) . " juta " . terbilang($angka % 1000000);
        } else {
            return "Angka terlalu besar";
        }
    }
}
