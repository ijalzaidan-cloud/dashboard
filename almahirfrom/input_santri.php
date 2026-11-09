<?php
require_once 'coneksi.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error = '';
$values = [
  'no_induk' => '',
  'nama_santri' => '',
  'tmpt_lahir' => '',
  'tgl_lahir' => '',
  'alamat' => '',
  'no_telp' => '',
  'wali_santri' => '',
  'no_telp_wali_santri' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($values as $field => $value) {
    $values[$field] = trim((string) ($_POST[$field] ?? ''));
  }

  $noInduk = filter_var($values['no_induk'], FILTER_VALIDATE_INT);
  if ($noInduk === false || $noInduk < 1) {
    $error = 'No induk harus berupa angka positif.';
  } elseif ($values['nama_santri'] === '') {
    $error = 'Nama santri wajib diisi.';
  } else {
    try {
      $stmt = $conn->prepare(
        'INSERT INTO tb_santri (no_induk, nama_santri, tmpt_lahir, tgl_lahir, alamat, no_telp, wali_santri, no_telp_wali_santri)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
      );
      $stmt->bind_param(
        'isssssss',
        $noInduk,
        $values['nama_santri'],
        $values['tmpt_lahir'],
        $values['tgl_lahir'],
        $values['alamat'],
        $values['no_telp'],
        $values['wali_santri'],
        $values['no_telp_wali_santri']
      );
      $stmt->execute();
      header('Location: data_santri.php?added=1');
      exit;
    } catch (mysqli_sql_exception $e) {
      error_log('Gagal menambahkan santri: ' . $e->getMessage());
      $error = $e->getCode() === 1062
        ? 'No induk tersebut sudah digunakan.'
        : 'Data santri gagal disimpan. Periksa kembali isian dan coba lagi.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Santri - Al Mahir</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css?v=dashboard">
</head>
<body>
  <aside class="sidebar">
    <div class="brand">
      <img src="img/favicon.png" alt="logo" class="brand-logo">
      <h2>AL MAHIR</h2>
    </div>
    <nav class="menu">
      <ul>
        <li><a href="index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li><a href="data_santri.php"><i class="fa-solid fa-users"></i> Data Santri</a></li>
        <li><a href="data_ustadz.php"><i class="fa-solid fa-user-tie"></i> Data Ustadz</a></li>
        <li><a href="data_mapel.php"><i class="fa-solid fa-book"></i> Mata Pelajaran</a></li>
        <li><a href="data_buku.php"><i class="fa-solid fa-file-lines"></i> Buku</a></li>
      </ul>
    </nav>
  </aside>

  <main class="content">
    <div class="sawit"><h1>Tambah Santri</h1></div>
    <section class="form-card">
      <h2>Formulir Data Santri</h2>
      <p class="form-intro">Isi informasi santri untuk menambahkannya ke daftar.</p>
      <?php if ($error !== ''): ?>
        <p class="form-message form-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>
      <form method="post" action="input_santri.php">
        <div class="form-grid">
          <label>No induk *
            <input type="number" name="no_induk" min="1" required value="<?= htmlspecialchars($values['no_induk'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Nama santri *
            <input type="text" name="nama_santri" maxlength="255" required value="<?= htmlspecialchars($values['nama_santri'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Tempat lahir
            <input type="text" name="tmpt_lahir" maxlength="255" value="<?= htmlspecialchars($values['tmpt_lahir'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Tanggal lahir
            <input type="date" name="tgl_lahir" value="<?= htmlspecialchars($values['tgl_lahir'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Alamat
            <input type="text" name="alamat" maxlength="255" value="<?= htmlspecialchars($values['alamat'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>No HP
            <input type="tel" name="no_telp" maxlength="20" value="<?= htmlspecialchars($values['no_telp'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Wali santri
            <input type="text" name="wali_santri" maxlength="50" value="<?= htmlspecialchars($values['wali_santri'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>No HP wali
            <input type="tel" name="no_telp_wali_santri" maxlength="20" value="<?= htmlspecialchars($values['no_telp_wali_santri'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
        </div>
        <p class="form-required-note">* Wajib diisi</p>
        <div class="form-actions">
          <a class="form-cancel" href="data_santri.php">Batal</a>
          <button type="submit" class="form-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Santri</button>
        </div>
      </form>
    </section>
  </main>
</body>
</html>
