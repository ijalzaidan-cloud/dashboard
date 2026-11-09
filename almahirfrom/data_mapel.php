<?php
require_once 'coneksi.php';
$result = $conn->query("SELECT * FROM tb_mapel");
if ($result === false) {
  error_log('Query tb_mapel gagal: ' . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Al Mahir</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
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

    <!-- 2. Halaman Data Santri -->
    <div id="dashboard" class="page-content default-page">
      <div class="sawit">
        <h1>Mata Pelajaran</h1>
      </div>
        <section class="section">
        <table class="tabel1">
            <thead class="sub">
                <tr>
                    <th>No</th>
                    <th>Nama Pelajaran</th>
                    <th>Jam Pelajaran</th>
                    <th>Kategori Pelajaran</th>
                </tr>
            </thead>

            <tbody>
              <?php if ($result === false): ?>
                <tr>
                  <td colspan="4">Data mata pelajaran gagal dimuat.</td>
                </tr>
              <?php elseif ($result->num_rows > 0): ?>
                <?php $no = 1; ?>
                <?php while ($mapel = $result->fetch_assoc()): ?>
                  <?php
                    $nomor = $mapel['no_mapel'] ?? $no;
                    $namaPelajaran = $mapel['name_mapel'];
                    $jamPelajaran = $mapel['jp_mapel'];
                    $kategoriPelajaran = $mapel['kategori_mapel'];
                  ?>
                  <tr>
                    <td><?= htmlspecialchars((string) $nomor, ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars((string) $namaPelajaran, ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars((string) $jamPelajaran, ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars((string) $kategoriPelajaran, ENT_QUOTES, 'UTF-8') ?></td>
                  </tr>
                  <?php $no++; ?>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                    <td colspan="4">Data mata pelajaran belum tersedia.</td>
                </tr>
              <?php endif; ?>
            </tbody>
        </table>
        </section>
      </div>
      
    </div>

    </main> 

</body>
</html>