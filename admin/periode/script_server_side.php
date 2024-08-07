<?php

/*
 * Skrip pemrosesan server-side DataTables.
 */

// Nama tabel utama
$table = 'periode';

// Kunci utama tabel
$primaryKey = 'id_periode';

// Kolom yang akan dibaca dan dikirimkan kembali ke DataTables
$columns = array(
    // array('db' => 'id_pendeta', 'dt' => 0),
    array('db' => 'jabatan_majelis', 'dt' => 0),
    array('db' => 'tanggal_menjabat', 'dt' => 1),
    array('db' => 'jabatan_pendeta', 'dt' => 2),
    array('db' => 'tanggal_jabatan_berahkir', 'dt' => 3),
    array('db' => 'id_periode', 'dt' => 4),
);

// Konfigurasi koneksi database
include_once '../_config/conn.php';

// Sertakan kelas SSP
require('../assets/libs/ssp.class.php');

$joinQuery = "FROM periode";

// Jalankan query dengan JOIN dan outputkan hasil dalam format JSON
echo json_encode(
    SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery)  // Menambahkan parameter JOIN
);
