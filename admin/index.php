<?php
require_once "../config/database.php";
require_once "../config/helpers.php";
require_admin();

$totalVoters = $conn->query("SELECT COUNT(*) c FROM polls")->fetch_assoc()['c'];
$totalRatings = $conn->query("SELECT COUNT(*) c FROM ratings")->fetch_assoc()['c'];

$summary = [];
$sql = "SELECT c.id, c.code, c.name,
        COALESCE(SUM(r.score),0) total_score,
        COALESCE(AVG(r.score),0) average_score,
        COUNT(r.id) rating_count
        FROM candidates c
        LEFT JOIN ratings r ON r.candidate_id = c.id
        GROUP BY c.id
        ORDER BY average_score DESC";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) $summary[] = $row;

$byIndicator = [];
$sql = "SELECT i.code, i.name AS indicator_name, c.code AS candidate_code,
        c.name AS candidate_name, COALESCE(AVG(r.score),0) avg_score
        FROM indicators i
        CROSS JOIN candidates c
        LEFT JOIN ratings r ON r.indicator_id=i.id AND r.candidate_id=c.id
        GROUP BY i.id, c.id
        ORDER BY i.id, c.id";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) $byIndicator[] = $row;

$recent = [];
$res = $conn->query("SELECT nama_penilai, nip, submitted_at FROM polls ORDER BY id DESC LIMIT 10");
while ($row = $res->fetch_assoc()) $recent[] = $row;
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Admin - Polling IST</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar bg-white border-bottom">
<div class="container py-2">
    <a class="navbar-brand fw-bold" href="index.php"><span class="brand-mark">IST</span> Dashboard Polling</a>
    <a class="btn btn-outline-danger btn-sm" href="logout.php">Keluar</a>
</div>
</nav>

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="eyebrow">ADMINISTRATOR</span>
            <h1 class="fw-bold mb-1">Rekapitulasi Penilaian</h1>
            <p class="text-muted mb-0">Pantau hasil polling Insan Statistik Teladan.</p>
        </div>
        <a class="btn btn-primary" href="../index.php">Lihat Form</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="stat-card"><span>Total Penilai</span><strong><?= e($totalVoters) ?></strong></div></div>
        <div class="col-md-4"><div class="stat-card"><span>Total Data Nilai</span><strong><?= e($totalRatings) ?></strong></div></div>
        <div class="col-md-4"><div class="stat-card"><span>Indikator × Kandidat</span><strong>18</strong></div></div>
    </div>

    <section class="card-modern mb-4">
        <div class="section-title">
            <span class="step">01</span>
            <div><h2>Ranking Kandidat</h2><p>Perhitungan menggunakan rata-rata seluruh nilai yang masuk.</p></div>
        </div>
        <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>#</th><th>Kandidat</th><th>Total Nilai</th><th>Rata-rata</th><th>Jumlah Penilaian</th></tr></thead>
            <tbody>
            <?php foreach ($summary as $i => $row): ?>
                <tr>
                    <td><span class="rank"><?= $i+1 ?></span></td>
                    <td><strong>Kandidat <?= e($row['code']) ?></strong><br><small><?= e($row['name']) ?></small></td>
                    <td><?= e($row['total_score']) ?></td>
                    <td><strong><?= number_format((float)$row['average_score'], 2) ?></strong> / 5</td>
                    <td><?= e($row['rating_count']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </section>

    <section class="card-modern mb-4">
        <div class="section-title">
            <span class="step">02</span>
            <div><h2>Rata-rata per Indikator</h2><p>Membantu melihat keunggulan setiap kandidat.</p></div>
        </div>
        <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead><tr><th>Indikator</th><th>A</th><th>B</th><th>C</th></tr></thead>
            <tbody>
            <?php
            $grouped = [];
            foreach ($byIndicator as $r) $grouped[$r['indicator_name']][$r['candidate_code']] = $r['avg_score'];
            foreach ($grouped as $indicator => $values):
            ?>
            <tr>
                <td><strong><?= e($indicator) ?></strong></td>
                <td><?= number_format($values['A'] ?? 0, 2) ?></td>
                <td><?= number_format($values['B'] ?? 0, 2) ?></td>
                <td><?= number_format($values['C'] ?? 0, 2) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </section>

    <section class="card-modern">
        <div class="section-title">
            <span class="step">03</span>
            <div><h2>Penilai Terbaru</h2><p>Daftar 10 pengiriman terakhir.</p></div>
        </div>
        <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Nama</th><th>NIP / ID</th><th>Waktu</th></tr></thead>
            <tbody>
            <?php foreach ($recent as $r): ?>
            <tr><td><?= e($r['nama_penilai']) ?></td><td><?= e($r['nip']) ?></td><td><?= e($r['submitted_at']) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </section>
</main>
</body>
</html>
