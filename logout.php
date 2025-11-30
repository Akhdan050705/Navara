<?php
require_once 'config.php'; // Memanggil config untuk memulai session

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session
session_destroy();

// Arahkan kembali ke halaman landing
header("Location: index.php");
exit;
?>