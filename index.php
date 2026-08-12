<?php
require_once "config/database.php";
require_once "config/helpers.php";

$candidates = [];
$result = $conn->query("SELECT * FROM candidates ORDER BY id");
while ($row = $result->fetch_assoc()) {
    $candidates[] = $row;
}

$indicators = [];
$result = $conn->query("SELECT * FROM indicators ORDER BY id");
while ($row = $result->fetch_assoc()) {
    $indicators[] = $row;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Penilaian Pegawai - IST Kabupaten Madiun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg border-bottom sticky-top">

    <div class="container py-2">

        <a
            class="navbar-brand fw-bold d-flex align-items-center"
            href="index.php"
        >

            <span class="brand-logo">
                IST
            </span>

            <span>
                Seleksi Insan Statistik Teladan
            </span>

        </a>


        <a
            href="admin/login.php"
            class="btn btn-outline-primary btn-sm"
        >

            <i class="bi bi-shield-lock me-1"></i>

            Admin

        </a>

    </div>

</nav>


<!-- ================= MAIN ================= -->

<main class="container py-4 py-lg-5">


    <!-- HERO -->

    <div class="hero-card mb-4">

        <span class="hero-badge">

            <i class="bi bi-clipboard-check"></i>

            INSTRUMEN PENILAIAN PEGAWAI

        </span>

        <h1 class="display-6 mb-2">

            Seleksi Insan Statistik Teladan (IST)

        </h1>

        <p class="mb-0">

            Kabupaten Madiun

        </p>

    </div>



    <form
        action="proses_polling.php"
        method="post"
        id="pollingForm"
    >


        <!-- ================= IDENTITAS ================= -->

        <section class="poll-card mb-4">

            <div class="section-header">

                <div class="section-number">
                    01
                </div>

                <div>

                    <h2>
                        Identitas Penilai
                    </h2>

                    <p>
                        Silakan isi identitas Anda sebelum memberikan penilaian.
                    </p>

                </div>

            </div>


            <div class="row g-3">

                <div class="col-md-6">

                    <label class="form-label">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="nama_penilai"
                        class="form-control form-control-lg"
                        placeholder="Masukkan nama lengkap"
                        maxlength="120"
                        required
                    >

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        NIP / ID Pegawai
                    </label>

                    <input
                        type="text"
                        name="nip"
                        class="form-control form-control-lg"
                        placeholder="Masukkan NIP / ID pegawai"
                        maxlength="50"
                        required
                    >

                </div>

            </div>


            <div class="alert alert-primary mt-4 mb-0 d-flex gap-2">

                <i class="bi bi-info-circle-fill"></i>

                <small>

                    Identitas digunakan untuk memastikan setiap staf
                    hanya memberikan penilaian satu kali.

                </small>

            </div>

        </section>



        <!-- ================= PETUNJUK ================= -->

        <section class="poll-card mb-4">

            <div class="section-header">

                <div class="section-number">
                    02
                </div>

                <div>

                    <h2>
                        Petunjuk Penilaian
                    </h2>

                    <p>
                        Berikan penilaian secara objektif kepada seluruh kandidat.
                    </p>

                </div>

            </div>


            <div class="row g-2">

                <div class="col-6 col-md">

                    <div class="scale-item">

                        <div class="scale-number">
                            1
                        </div>

                        <small>
                            Sangat Kurang
                        </small>

                    </div>

                </div>


                <div class="col-6 col-md">

                    <div class="scale-item">

                        <div class="scale-number">
                            2
                        </div>

                        <small>
                            Kurang
                        </small>

                    </div>

                </div>


                <div class="col-6 col-md">

                    <div class="scale-item">

                        <div class="scale-number">
                            3
                        </div>

                        <small>
                            Cukup
                        </small>

                    </div>

                </div>


                <div class="col-6 col-md">

                    <div class="scale-item">

                        <div class="scale-number">
                            4
                        </div>

                        <small>
                            Baik
                        </small>

                    </div>

                </div>


                <div class="col-12 col-md">

                    <div class="scale-item">

                        <div class="scale-number">
                            5
                        </div>

                        <small>
                            Sangat Baik
                        </small>

                    </div>

                </div>

            </div>

        </section>



        <!-- ================= PENILAIAN ================= -->

<?php
$currentCategory = null;

foreach ($indicators as $indicator):

    /*
     * Jika masuk kategori baru,
     * tutup kategori sebelumnya terlebih dahulu.
     */
    if ($currentCategory !== $indicator['category']):

        if ($currentCategory !== null):
?>
                </tbody>
            </table>
        </div>
    </section>
<?php
        endif;

        $currentCategory = $indicator['category'];
?>

    <!-- CATEGORY BARU -->

    <section class="poll-card mb-4">

        <div class="category-header">

            <div>

                <div class="category-title">
                    KATEGORI <?= e($indicator['category_no']) ?>
                </div>

                <h2>
                    <?= e($indicator['category']) ?>
                </h2>

                <p>
                    <?= e($indicator['focus']) ?>
                </p>

            </div>

            <div class="category-icon">

                <?= $indicator['category_no'] == 1 ? '🧠' : '✨' ?>

            </div>

        </div>


        <!-- TABLE -->

        <div class="table-responsive">

            <table class="table rating-table align-middle mb-0">

                <thead>

                    <tr>

                        <th style="min-width:380px">
                            Indikator Penilaian
                        </th>

                        <?php foreach ($candidates as $candidate): ?>

                            <th class="text-center">

                                Kandidat
                                <?= e($candidate['code']) ?>

                                <small>
                                    <?= e($candidate['name']) ?>
                                </small>

                            </th>

                        <?php endforeach; ?>

                    </tr>

                </thead>

                <tbody>

                <?php
                    endif;
                ?>

                    <!-- INDIKATOR -->

                    <tr>

                        <td>

                            <div class="indicator-code">
                                <?= e($indicator['code']) ?>
                            </div>

                            <div class="indicator-name">
                                <?= e($indicator['name']) ?>
                            </div>

                            <div class="indicator-description">
                                <?= e($indicator['description']) ?>
                            </div>

                        </td>


                        <!-- NILAI SETIAP KANDIDAT -->

                        <?php foreach ($candidates as $candidate): ?>

                            <td class="text-center">

                                <div class="rating-options">

                                    <?php
                                    for ($score = 1; $score <= 5; $score++):
                                        $field =
                                            "nilai_{$candidate['id']}_{$indicator['id']}";
                                    ?>

                                        <label>

                                            <input
                                                type="radio"
                                                name="<?= $field ?>"
                                                value="<?= $score ?>"
                                                required
                                            >

                                            <span>
                                                <?= $score ?>
                                            </span>

                                        </label>

                                    <?php endfor; ?>

                                </div>

                            </td>

                        <?php endforeach; ?>

                    </tr>

                <?php endforeach; ?>


                </tbody>

            </table>

        </div>

    </section>
        <!-- ================= SUBMIT ================= -->

        <div class="submit-card mb-5">

            <div>

                <h6>

                    <i class="bi bi-check-circle me-1 text-primary"></i>

                    Sudah selesai melakukan penilaian?

                </h6>

                <p>

                    Pastikan seluruh nilai sudah sesuai sebelum dikirim.

                </p>

            </div>


            <button
                type="submit"
                class="btn btn-primary btn-submit"
            >

                <i class="bi bi-send me-1"></i>

                Kirim Penilaian

            </button>

        </div>


    </form>

</main>



<!-- ================= FOOTER ================= -->

<footer>

    <div class="container py-4 text-center">

        <i class="bi bi-bar-chart-fill me-1"></i>

        Sistem Polling IST Kabupaten Madiun

        &copy;

        <?= date('Y') ?>

    </div>

</footer>


<!-- ================= JAVASCRIPT ================= -->

<script>

document
    .getElementById('pollingForm')
    .addEventListener('submit', function(e) {

        const ok = confirm(
            'Yakin semua penilaian sudah benar?\n\n' +
            'Penilaian yang sudah dikirim tidak dapat diubah.'
        );

        if (!ok) {
            e.preventDefault();
        }

    });

</script>

</body>
</html>
