<?php
// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Tanggal hari ini
$today = date('Y-m-d');
$currentMonth = date('m', strtotime($today)); // Bulan saat ini
$currentYear = date('Y', strtotime($today));  // Tahun saat ini
$currentDay = date('d', strtotime($today));   // Hari saat ini

if ($currentDay <= 5) {
    // Jika tanggal berada di periode 1-5 bulan
    $startDate = date('Y-m-01', strtotime('first day of last month')); // Awal bulan sebelumnya
    $endDate = $today; // Sampai tanggal hari ini
} else {
    // Jika tanggal melebihi tanggal 5
    $startDate = date('Y-m-01'); // Awal bulan ini
    $endDate = $today; // Sampai tanggal hari ini
}

// Menampilkan hasil
echo "Tanggal mulai: " . $startDate . PHP_EOL;
echo "Tanggal akhir: " . $endDate . PHP_EOL;
?>
