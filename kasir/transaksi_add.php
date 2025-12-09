<?php
session_start();
require "../koneksi/koneksi.php";

// Keranjang tidak boleh kosong
if (!isset($_SESSION['keranjang']) || count($_SESSION['keranjang']) == 0) {
    header("Location: transaksi.php");
    exit;
}

// Data POST
$total = $_POST['total'];
$pembayaran = $_POST['pembayaran'];

if ($pembayaran < $total) {
    echo "<script>
            alert('Pembayaran kurang dari total!');
            window.location='transaksi.php';
          </script>";
    exit;
}

$kembali = $pembayaran - $total;

// ===============================
// 1. SIMPAN TRANSAKSI
// ===============================
$id_user = $_SESSION['id_user']; // kasir

$conn->query("
    INSERT INTO transaksi (id_user, total_harga, pembayaran, kembali)
    VALUES ('$id_user', '$total', '$pembayaran', '$kembali')
");

$id_transaksi = $conn->insert_id; // ambil ID transaksi terbaru

// ===============================
// 2. SIMPAN DETAIL TRANSAKSI
// ===============================
foreach ($_SESSION['keranjang'] as $item) {

    $id_menu = $item['id_menu'];
    $qty = $item['qty'];
    $subtotal = $item['subtotal'];

    $conn->query("
        INSERT INTO detail_transaksi (id_transaksi, id_menu, qty, subtotal)
        VALUES ('$id_transaksi', '$id_menu', '$qty', '$subtotal')
    ");
}

// ===============================
// 3. BERSIHKAN KERANJANG
// ===============================
unset($_SESSION['keranjang']);

// ===============================
// 4. REDIRECT DENGAN NOTIF
// ===============================
// simpan informasi untuk modal
$_SESSION['transaksi_sukses'] = [
    "kembali" => $kembali,
    "total" => $total
];

header("Location: transaksi.php");
exit;

?>