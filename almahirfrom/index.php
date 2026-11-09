<?php
require_once 'coneksi.php';

function dashboardQuery(mysqli $conn, string $sql): ?mysqli_result
{
  try {
    $result = $conn->query($sql);
    if ($result === false) {
      error_log('Query dashboard gagal: ' . $conn->error);
      return null;
    }

    return $result;
  } catch (mysqli_sql_exception $e) {
    error_log('Query dashboard gagal: ' . $e->getMessage());
    return null;
  }
}

function dashboardCount(mysqli $conn, string $table): ?int
{
  $result = dashboardQuery($conn, "SELECT COUNT(*) AS total FROM `$table`");
  if ($result === null) {
    return null;
  }

  $row = $result->fetch_assoc();
  return $row === null ? null : (int) $row['total'];
}

$total_santri = dashboardCount($conn, 'tb_santri');
$total_ustadz = dashboardCount($conn, 'tb_ustadz');
$total_mapel = dashboardCount($conn, 'tb_mapel');
$total_buku = dashboardCount($conn, 'tb_buku');

$kategori = [];
$kategoriQuery = dashboardQuery(
  $conn,
  'SELECT kategori_mapel, COUNT(*) AS jml FROM tb_mapel GROUP BY kategori_mapel'
);
if ($kategoriQuery !== null) {
  while ($row = $kategoriQuery->fetch_assoc()) {
    $kategori[] = $row;
  }
}

$ustadz_list = [];
$ustadzQuery = dashboardQuery($conn, 'SELECT nama_ustadz, jabatan FROM tb_ustadz LIMIT 5');
if ($ustadzQuery !== null) {
  while ($row = $ustadzQuery->fetch_assoc()) {
    $ustadz_list[] = $row;
  }
}

$santri_list = [];
$santriQuery = dashboardQuery(
  $conn,
  'SELECT nama_santri FROM tb_santri ORDER BY no_induk DESC LIMIT 5'
);
if ($santriQuery !== null) {
  while ($row = $santriQuery->fetch_assoc()) {
    $santri_list[] = $row;
  }
}

$maks_kat = 1;
foreach ($kategori as $item) {
  $maks_kat = max($maks_kat, (int) $item['jml']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Al Mahir</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css?v=dashboard">
</head>
<body>

  <!-- Sidebar / Navigasi Kiri -->
  <aside class="sidebar">
    <div class="brand">
      <img src="img/favicon.png" alt="logo" class="brand-logo">
      <h2>AL MAHIR</h2>
    </div>

    <div class="search-box">
      <input type="text" placeholder="Cari menu...">
    </div>

    <!-- Tautan menu menggunakan href="#id" -->
    <nav class="menu">
      <ul>
          <li><a href="index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li><a href="data_santri.php"><i class="fa-solid fa-users"></i> Data Santri</a></li>
        <li><a href="data_ustadz.php"><i class="fa-solid fa-user-tie"></i> Data Ustadz</a></li>
        <li><a href="data_mapel.php"><i class="fa-solid fa-book"></i> Mata Pelajaran</a></li>
        <li><a href="data_buku.php"><i class="fa-solid fa-file-lines"></i> Buku</a></li>
      </ul>
    </nav>

    <div class="user-profile">
      <div class="avatar">AD</div>
      <div class="user-info">
        <span class="name">Admin</span>
        <span class="email">ijalzaidan@gmail.com</span>
        <a href="#" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
      </div>
    </div>
  </aside>

  <main class="content">
    <div id="dashboard" class="page-content default-page">
      <div class="sawit">
        <h1>Dashboard</h1>
      </div>
<div class="dash-welcome">
  <h3>Selamat datang, Admin</h3>
  <p><?= date('d-m-Y') ?> &middot; Ringkasan data Pondok Al Mahir</p>
</div>

<!-- Kartu ringkasan -->
<div class="dash-cards">
  <a class="dash-card" href="data_santri.php">
    <div class="dash-icon"><i class="fa-solid fa-users"></i></div>
    <div><span class="dash-num"><?= $total_santri === null ? '&mdash;' : $total_santri ?></span><span class="dash-label">Total Santri</span></div>
  </a>
  <a class="dash-card" href="data_ustadz.php">
    <div class="dash-icon"><i class="fa-solid fa-user-tie"></i></div>
    <div><span class="dash-num"><?= $total_ustadz === null ? '&mdash;' : $total_ustadz ?></span><span class="dash-label">Total Ustadz</span></div>
  </a>
  <a class="dash-card" href="data_mapel.php">
    <div class="dash-icon"><i class="fa-solid fa-book-open"></i></div>
    <div><span class="dash-num"><?= $total_mapel === null ? '&mdash;' : $total_mapel ?></span><span class="dash-label">Mata Pelajaran</span></div>
  </a>
  <a class="dash-card" href="data_buku.php">
    <div class="dash-icon"><i class="fa-solid fa-book"></i></div>
    <div><span class="dash-num"><?= $total_buku === null ? '&mdash;' : $total_buku ?></span><span class="dash-label">Total Buku</span></div>
  </a>
</div>

<!-- Panel bawah -->
<div class="dash-grid">
  <div class="dash-panel">
    <h4>Mapel per Kategori</h4>
    <?php if ($kategoriQuery === null): ?>
      <p class="dash-empty">Data kategori gagal dimuat.</p>
    <?php elseif (!$kategori): ?>
      <p class="dash-empty">Belum ada data.</p>
    <?php endif; ?>
    <?php foreach ($kategori as $k): ?>
      <div class="bar-row">
        <span><?= htmlspecialchars((string) $k['kategori_mapel'], ENT_QUOTES, 'UTF-8') ?></span>
        <div class="bar"><div style="width:<?= ((int) $k['jml'] / $maks_kat) * 100 ?>%"></div></div>
        <b><?= $k['jml'] ?></b>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="dash-panel">
    <h4>Ustadz</h4>
    <?php if ($ustadzQuery === null): ?>
      <p class="dash-empty">Data ustadz gagal dimuat.</p>
    <?php elseif (!$ustadz_list): ?>
      <p class="dash-empty">Belum ada data.</p>
    <?php endif; ?>
    <ul class="dash-list">
      <?php foreach ($ustadz_list as $u): ?>
        <li><?= htmlspecialchars((string) $u['nama_ustadz'], ENT_QUOTES, 'UTF-8') ?><small><?= htmlspecialchars((string) $u['jabatan'], ENT_QUOTES, 'UTF-8') ?></small></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="dash-panel">
    <h4>Santri Terbaru</h4>
    <?php if ($santriQuery === null): ?>
      <p class="dash-empty">Data santri gagal dimuat.</p>
    <?php elseif (!$santri_list): ?>
      <p class="dash-empty">Belum ada data.</p>
    <?php endif; ?>
    <ul class="dash-list">
      <?php foreach ($santri_list as $s): ?>
        <li><?= htmlspecialchars((string) $s['nama_santri'], ENT_QUOTES, 'UTF-8') ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<!-- Pintasan cepat -->
<div class="dash-quick">
  <a href="input_santri.php">+ Tambah Santri</a>
  <a href="input_ustadz.php">+ Tambah Ustadz</a>
</div>
    </div>
  </main>

</body>
</html>