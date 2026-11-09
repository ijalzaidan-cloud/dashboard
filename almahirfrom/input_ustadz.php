<?php
require_once 'coneksi.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error = '';
$values = [
  'nip' => '',
  'nik_ktp' => '',
  'nama_ustadz' => '',
  'email' => '',
  'no_telp' => '',
  'alamat' => '',
  'tgl_lahir' => '',
  'jabatan' => '',
  'status_karyawan' => '',
  'tgl_masuk' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($values as $field => $value) {
    $values[$field] = trim((string) ($_POST[$field] ?? ''));
  }

  $nip = filter_var($values['nip'], FILTER_VALIDATE_INT);
  if ($nip === false || $nip < 1) {
    $error = 'NIP harus berupa angka positif.';
  } elseif ($values['nik_ktp'] === '') {
    $error = 'NIK KTP wajib diisi.';
  } elseif (strlen($values['nik_ktp']) > 255) {
    $error = 'NIK KTP maksimal 255 karakter.';
  } elseif ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
    $error = 'Format email tidak valid.';
  } else {
    $tglLahir = $values['tgl_lahir'] === '' ? null : $values['tgl_lahir'];
    $tglMasuk = $values['tgl_masuk'] === '' ? null : $values['tgl_masuk'];

    try {
      $stmt = $conn->prepare(
        'INSERT INTO tb_ustadz (nip, nik_ktp, nama_ustadz, email, no_telp, alamat, tgl_lahir, jabatan, status_karyawan, tgl_masuk)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
      );
      $stmt->bind_param(
        'isssssssss',
        $nip,
        $values['nik_ktp'],
        $values['nama_ustadz'],
        $values['email'],
        $values['no_telp'],
        $values['alamat'],
        $tglLahir,
        $values['jabatan'],
        $values['status_karyawan'],
        $tglMasuk
      );
      $stmt->execute();
      header('Location: data_ustadz.php?added=1');
      exit;
    } catch (mysqli_sql_exception $e) {
      error_log('Gagal menambahkan ustadz: ' . $e->getMessage());
      $error = $e->getCode() === 1062
        ? 'NIP tersebut sudah digunakan.'
        : 'Data ustadz gagal disimpan. Periksa kembali isian dan coba lagi.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Ustadz - Al Mahir</title>
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
    <div class="sawit"><h1>Tambah Ustadz</h1></div>
    <section class="form-card">
      <h2>Formulir Data Ustadz</h2>
      <p class="form-intro">Isi informasi ustadz untuk menambahkannya ke daftar.</p>
      <?php if ($error !== ''): ?>
        <p class="form-message form-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>
      <form method="post" action="input_ustadz.php">
        <div class="form-grid">
          <label>NIP *
            <input type="number" name="nip" min="1" required value="<?= htmlspecialchars($values['nip'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>NIK KTP *
            <input type="text" name="nik_ktp" maxlength="255" required value="<?= htmlspecialchars($values['nik_ktp'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Nama ustadz
            <input type="text" name="nama_ustadz" maxlength="255" value="<?= htmlspecialchars($values['nama_ustadz'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Email
            <input type="email" name="email" maxlength="255" value="<?= htmlspecialchars($values['email'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>No HP
            <input type="tel" name="no_telp" maxlength="255" value="<?= htmlspecialchars($values['no_telp'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Alamat
            <textarea name="alamat" rows="3"><?= htmlspecialchars($values['alamat'], ENT_QUOTES, 'UTF-8') ?></textarea>
          </label>
          <label>Tanggal lahir
            <input type="date" name="tgl_lahir" value="<?= htmlspecialchars($values['tgl_lahir'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Jabatan
            <input type="text" name="jabatan" maxlength="50" value="<?= htmlspecialchars($values['jabatan'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Status karyawan / bidang
            <input type="text" name="status_karyawan" maxlength="50" value="<?= htmlspecialchars($values['status_karyawan'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Tanggal masuk
            <input type="date" name="tgl_masuk" value="<?= htmlspecialchars($values['tgl_masuk'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
        </div>
        <p class="form-required-note">* Wajib diisi</p>
        <div class="form-actions">
          <a class="form-cancel" href="data_ustadz.php">Batal</a>
          <button type="submit" class="form-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Ustadz</button>
        </div>
      </form>
    </section>
  </main>
</body>
</html>
