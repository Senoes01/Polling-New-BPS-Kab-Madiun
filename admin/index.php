<?php
require_once "../config/database.php";
require_once "../config/helpers.php";
require_admin();


// =====================================================
// DATA STATISTIK 
// =====================================================

$totalVoters = $conn->query(
    "SELECT COUNT(*) c FROM polls"
)->fetch_assoc()['c'];

$totalRatings = $conn->query(
    "SELECT COUNT(*) c FROM ratings"
)->fetch_assoc()['c'];


// =====================================================
// RANKING KANDIDAT
// =====================================================

$summary = [];

$sql = "SELECT 
            c.id,
            c.code,
            c.name,
            COALESCE(SUM(r.score), 0) AS total_score,
            COALESCE(AVG(r.score), 0) AS average_score,
            COUNT(r.id) AS rating_count
        FROM candidates c
        LEFT JOIN ratings r 
            ON r.candidate_id = c.id
        GROUP BY c.id
        ORDER BY average_score DESC";

$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $summary[] = $row;
}


// =====================================================
// HASIL PEMENANG
// =====================================================

$showWinner = isset($_GET['hitung']) && $_GET['hitung'] == '1';

$winnerList = [];

if ($showWinner && count($summary) > 0) {

    usort($summary, function ($a, $b) {

        return (float)$b['average_score']
            <=> (float)$a['average_score'];

    });

    // Ambil maksimal 3 kandidat teratas
    $winnerList = array_slice($summary, 0, 3);
}


// =====================================================
// RATA-RATA PER INDIKATOR
// =====================================================

$byIndicator = [];

$sql = "SELECT 
            i.code,
            i.name AS indicator_name,
            c.code AS candidate_code,
            c.name AS candidate_name,
            COALESCE(AVG(r.score), 0) AS avg_score
        FROM indicators i
        CROSS JOIN candidates c
        LEFT JOIN ratings r 
            ON r.indicator_id = i.id 
            AND r.candidate_id = c.id
        GROUP BY i.id, c.id
        ORDER BY i.id, c.id";

$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $byIndicator[] = $row;
}


// =====================================================
// PENILAI TERBARU
// =====================================================

$recent = [];

$res = $conn->query(
    "SELECT 
        nama_penilai,
        nip,
        submitted_at
     FROM polls
     ORDER BY id DESC
     LIMIT 10"
);

while ($row = $res->fetch_assoc()) {
    $recent[] = $row;
}


// =====================================================
// JUMLAH INDIKATOR × KANDIDAT
// =====================================================

$totalIndicatorCandidate = count($byIndicator);

?>

<!doctype html>

<html lang="id">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Dashboard Admin - Polling IST</title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >


    <!-- CSS Project -->

    <link
        href="../assets/style.css"
        rel="stylesheet"
    >

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="navbar bg-white border-bottom">

    <div class="container py-2">

        <a
            class="navbar-brand fw-bold"
            href="index.php"
        >

            <span class="brand-mark">
                IST
            </span>

            Dashboard Polling

        </a>


        <a
            class="btn btn-outline-danger btn-sm"
            href="logout.php"
        >

            <i class="bi bi-box-arrow-right me-1"></i>

            Keluar

        </a>

    </div>

</nav>



<!-- =====================================================
     MAIN
===================================================== -->

<main class="container py-4">


    <!-- =================================================
         HEADER
    ================================================== -->

    <div class="d-flex justify-content-between align-items-end mb-4">

        <div>

            <span class="eyebrow">
                ADMINISTRATOR
            </span>

            <h1 class="fw-bold mb-1">
                Rekapitulasi Penilaian
            </h1>

            <p class="text-muted mb-0">
                Pantau hasil polling Insan Statistik Teladan.
            </p>

        </div>


        <div class="d-flex gap-2">

            <!-- Lihat Form -->
            <a
                class="btn btn-outline-secondary"
                href="../index.php"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Lihat Form
            </a>

            <!-- Export CSV -->
            <a
                class="btn btn-success"
                href="export_csv.php"
            >
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                Export CSV
            </a>

            <!-- Hitung Pemenang -->
            <button
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#modalHitungPemenang"
            >
                <i class="bi bi-trophy me-1"></i>
                Hitung Pemenang
            </button>

        </div>

    </div>


    <!-- =================================================
         STATISTIK
    ================================================== -->

    <div class="row g-3 mb-4">


        <!-- Total Penilai -->

        <div class="col-md-4">

            <div class="stat-card">

                <span>
                    Total Penilai
                </span>

                <strong>
                    <?= e($totalVoters) ?>
                </strong>

            </div>

        </div>


        <!-- Total Data Nilai -->

        <div class="col-md-4">

            <div class="stat-card">

                <span>
                    Total Data Nilai
                </span>

                <strong>
                    <?= e($totalRatings) ?>
                </strong>

            </div>

        </div>


        <!-- Indikator × Kandidat -->

        <div class="col-md-4">

            <div class="stat-card">

                <span>
                    Indikator × Kandidat
                </span>

                <strong>
                    <?= e($totalIndicatorCandidate) ?>
                </strong>

            </div>

        </div>

    </div>



    <!-- =================================================
         HASIL PEMENANG
         Hanya muncul setelah tombol ditekan
    ================================================== -->

    <?php if ($showWinner): ?>


        <section class="card-modern mb-4 winner-section">


            <!-- Header -->

            <div class="section-title">

                <span class="step">
                    🏆
                </span>

                <div>

                    <h2>
                        Hasil Pegawai Terbaik
                    </h2>

                    <p>
                        Peringkat berdasarkan rata-rata seluruh
                        penilaian yang telah masuk ke sistem.
                    </p>

                </div>

            </div>



            <?php if (count($winnerList) > 0): ?>


                <!-- =================================================
                     JUARA 1 / PEMENANG UTAMA
                ================================================== -->

                <div class="winner-main">


                    <div class="winner-trophy">
                        🏆
                    </div>


                    <div class="winner-label">
                        PEGAWAI TERBAIK
                    </div>


                    <h1>

                        Kandidat
                        <?= e($winnerList[0]['code']) ?>

                    </h1>


                    <h3>

                        <?= e($winnerList[0]['name']) ?>

                    </h3>


                    <div class="winner-score">

                        <?= number_format(
                            (float)$winnerList[0]['average_score'],
                            2
                        ) ?>

                        <span>
                            / 5
                        </span>

                    </div>


                    <p class="text-muted">
                        Rata-rata penilaian
                    </p>

                </div>



                <!-- =================================================
                     DETAIL PEMENANG
                ================================================== -->

                <div class="row g-3 mb-4">


                    <!-- Total Nilai -->

                    <div class="col-md-4">

                        <div class="stat-card">

                            <span>
                                Total Nilai
                            </span>

                            <strong>

                                <?= e(
                                    $winnerList[0]['total_score']
                                ) ?>

                            </strong>

                        </div>

                    </div>


                    <!-- Jumlah Penilaian -->

                    <div class="col-md-4">

                        <div class="stat-card">

                            <span>
                                Jumlah Penilaian
                            </span>

                            <strong>

                                <?= e(
                                    $winnerList[0]['rating_count']
                                ) ?>

                            </strong>

                        </div>

                    </div>


                    <!-- Persentase -->

                    <div class="col-md-4">

                        <div class="stat-card">

                            <span>
                                Persentase
                            </span>

                            <strong>

                                <?= number_format(
                                    (
                                        (float)$winnerList[0]['average_score']
                                        / 5
                                    ) * 100,
                                    1
                                ) ?>%

                            </strong>

                        </div>

                    </div>

                </div>



                <!-- =================================================
                     RANKING 1 - 3
                ================================================== -->

                <h5 class="fw-bold mb-3">

                    <i class="bi bi-award me-1"></i>

                    Peringkat Kandidat

                </h5>


                <div class="row g-3">


                    <?php foreach (
                        $winnerList as $index => $row
                    ): ?>


                        <?php

                        $rank = $index + 1;


                        if ($rank == 1) {

                            $medal = "🥇";
                            $rankText = "Juara 1";

                        } elseif ($rank == 2) {

                            $medal = "🥈";
                            $rankText = "Juara 2";

                        } else {

                            $medal = "🥉";
                            $rankText = "Juara 3";

                        }


                        $percentage =
                            (
                                (float)$row['average_score']
                                / 5
                            ) * 100;

                        ?>


                        <div class="col-md-4">


                            <div class="ranking-card">


                                <div class="ranking-medal">

                                    <?= $medal ?>

                                </div>


                                <div class="ranking-position">

                                    <?= $rankText ?>

                                </div>


                                <h4>

                                    Kandidat
                                    <?= e($row['code']) ?>

                                </h4>


                                <p class="mb-3">

                                    <?= e($row['name']) ?>

                                </p>


                                <div class="ranking-score">

                                    <?= number_format(
                                        (float)$row['average_score'],
                                        2
                                    ) ?>

                                    <small>
                                        / 5
                                    </small>

                                </div>


                                <!-- Progress -->

                                <div class="progress mt-3">

                                    <div
                                        class="progress-bar"
                                        role="progressbar"
                                        style="width: <?= $percentage ?>%"
                                        aria-valuenow="<?= $percentage ?>"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                    >
                                    </div>

                                </div>


                                <small class="text-muted">

                                    <?= number_format(
                                        $percentage,
                                        1
                                    ) ?>%

                                    dari skor maksimal

                                </small>


                            </div>

                        </div>


                    <?php endforeach; ?>


                </div>


            <?php else: ?>


                <!-- Belum ada data -->

                <div class="alert alert-warning">

                    <i class="bi bi-exclamation-triangle me-2"></i>

                    Belum ada data penilaian untuk menentukan
                    pemenang.

                </div>


            <?php endif; ?>


        </section>


    <?php endif; ?>



    <!-- =================================================
         RANKING KANDIDAT
    ================================================== -->

    <section class="card-modern mb-4">


        <div class="section-title">

            <span class="step">
                01
            </span>

            <div>

                <h2>
                    Ranking Kandidat
                </h2>

                <p>
                    Perhitungan menggunakan rata-rata seluruh
                    nilai yang masuk.
                </p>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table align-middle">


                <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Kandidat
                        </th>

                        <th>
                            Total Nilai
                        </th>

                        <th>
                            Rata-rata
                        </th>

                        <th>
                            Jumlah Penilaian
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach (
                        $summary as $i => $row
                    ): ?>


                        <tr>


                            <td>

                                <span class="rank">

                                    <?= $i + 1 ?>

                                </span>

                            </td>


                            <td>

                                <strong>

                                    Kandidat
                                    <?= e($row['code']) ?>

                                </strong>

                                <br>

                                <small>

                                    <?= e($row['name']) ?>

                                </small>

                            </td>


                            <td>

                                <?= e(
                                    $row['total_score']
                                ) ?>

                            </td>


                            <td>

                                <strong>

                                    <?= number_format(
                                        (float)$row['average_score'],
                                        2
                                    ) ?>

                                </strong>

                                / 5

                            </td>


                            <td>

                                <?= e(
                                    $row['rating_count']
                                ) ?>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>

            </table>

        </div>

    </section>



    <!-- =================================================
         RATA-RATA PER INDIKATOR
    ================================================== -->

    <section class="card-modern mb-4">


        <div class="section-title">

            <span class="step">
                02
            </span>

            <div>

                <h2>
                    Rata-rata per Indikator
                </h2>

                <p>
                    Membantu melihat keunggulan setiap kandidat.
                </p>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table table-sm align-middle">


                <thead>

                    <tr>

                        <th>
                            Indikator
                        </th>

                        <th>
                            A
                        </th>

                        <th>
                            B
                        </th>

                        <th>
                            C
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php

                    $grouped = [];


                    foreach ($byIndicator as $r) {

                        $grouped[
                            $r['indicator_name']
                        ][
                            $r['candidate_code']
                        ] = $r['avg_score'];

                    }


                    foreach (
                        $grouped as $indicator => $values
                    ):

                    ?>


                        <tr>


                            <td>

                                <strong>

                                    <?= e($indicator) ?>

                                </strong>

                            </td>


                            <td>

                                <?= number_format(
                                    $values['A'] ?? 0,
                                    2
                                ) ?>

                            </td>


                            <td>

                                <?= number_format(
                                    $values['B'] ?? 0,
                                    2
                                ) ?>

                            </td>


                            <td>

                                <?= number_format(
                                    $values['C'] ?? 0,
                                    2
                                ) ?>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>

            </table>

        </div>

    </section>



    <!-- =================================================
         PENILAI TERBARU
    ================================================== -->

    <section class="card-modern">


        <div class="section-title">

            <span class="step">
                03
            </span>

            <div>

                <h2>
                    Penilai Terbaru
                </h2>

                <p>
                    Daftar 10 pengiriman terakhir.
                </p>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table align-middle">


                <thead>

                    <tr>

                        <th>
                            Nama
                        </th>

                        <th>
                            NIP / ID
                        </th>

                        <th>
                            Waktu
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach ($recent as $r): ?>


                        <tr>

                            <td>

                                <?= e(
                                    $r['nama_penilai']
                                ) ?>

                            </td>


                            <td>

                                <?= e(
                                    $r['nip']
                                ) ?>

                            </td>


                            <td>

                                <?= e(
                                    $r['submitted_at']
                                ) ?>

                            </td>

                        </tr>


                    <?php endforeach; ?>


                </tbody>

            </table>

        </div>

    </section>


</main>



<!-- =====================================================
     MODAL KONFIRMASI HITUNG PEMENANG
===================================================== -->

<div
    class="modal fade"
    id="modalHitungPemenang"
    tabindex="-1"
    aria-labelledby="modalHitungPemenangLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content winner-modal">


            <!-- HEADER -->

            <div class="modal-header border-0">

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <!-- BODY -->

            <div class="modal-body text-center px-4 pb-4">


                <div class="modal-icon">

                    <i class="bi bi-trophy-fill"></i>

                </div>


                <h4
                    class="fw-bold mt-3 mb-2"
                    id="modalHitungPemenangLabel"
                >

                    Hitung Pemenang?

                </h4>


                <p class="text-muted mb-3">

                    Sistem akan menghitung peringkat kandidat
                    berdasarkan rata-rata seluruh penilaian
                    yang telah masuk.

                </p>


                <div class="modal-info">

                    <i class="bi bi-info-circle-fill"></i>

                    <span>

                        Pastikan seluruh data penilaian
                        sudah selesai sebelum melanjutkan.

                    </span>

                </div>


            </div>


            <!-- FOOTER -->

            <div
                class="modal-footer border-0 justify-content-center gap-2 pb-4"
            >


                <button
                    type="button"
                    class="btn btn-light modal-cancel"
                    data-bs-dismiss="modal"
                >

                    Batal

                </button>


                <a
                    href="index.php?hitung=1"
                    class="btn btn-primary modal-confirm"
                >

                    <i class="bi bi-trophy me-1"></i>

                    Ya, Hitung Pemenang

                </a>


            </div>


        </div>

    </div>

</div>


<!-- =====================================================
     BOOTSTRAP JAVASCRIPT
===================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>