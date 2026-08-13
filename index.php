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
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>
        Penilaian Pegawai - IST Kabupaten Madiun
    </title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >
    <link
        href="assets/style.css"
        rel="stylesheet"
    >

    <!-- ================= VALIDATION STYLE ================= -->
    <style>
        /* Input nama / NIP yang belum diisi */
        .input-error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, 0.25) !important;
        }

        /* Kotak pilihan rating (1-5) yang belum dipilih */
        .rating-options.rating-error {
            border: 1px solid #dc3545;
            border-radius: 8px;
            padding: 4px;
            background-color: rgba(220, 53, 69, 0.06);
        }

        /* Baris indikator yang belum lengkap dinilai */
        tr.indicator-error {
            background-color: rgba(220, 53, 69, 0.06);
        }

        tr.indicator-error .indicator-name {
            color: #dc3545;
        }

        /* Alert peringatan validasi */
        #validationAlert {
            display: none;
        }

        #validationAlert.show {
            display: flex;
        }
    </style>
</head>

<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg border-bottom sticky-top">
    <div class="container py-2">

        <a
            class="navbar-brand fw-bold d-flex align-items-center me-auto"
            href="index.php"
        >
            <img
                src="assets/logo_bps.png"
                alt="Logo Badan Pusat Statistik"
                class="brand-logo-img"
            >
            <span>
                Badan Pusat Statistik Kabupaten Madiun
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

    <!-- ================= HERO ================= -->

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

    <!-- ================= ALERT VALIDASI ================= -->
    <div
        id="validationAlert"
        class="alert alert-danger align-items-start gap-2 mb-4"
        role="alert"
    >
        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
        <div>
            <strong>Formulir belum lengkap.</strong>
            <div id="validationAlertText">
                Mohon lengkapi nama, NIP, dan seluruh penilaian
                (setiap indikator untuk setiap kandidat) sebelum mengirim.
            </div>
        </div>
    </div>

    <!-- ================= FORM ================= -->
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
                <!-- NAMA -->
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

                <!-- NIP -->
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

        <!-- ================= SKALA ================= -->
        <section class="poll-card mb-4">
            <div class="section-header">
                <div class="section-number">
                    02
                </div>
                <div>
                    <h2>
                        Skala Penilaian
                    </h2>

                    <p>
                        Berikan penilaian secara objektif kepada seluruh kandidat
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
             * Ketika masuk kategori baru,
             * tutup kategori sebelumnya.
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

        <!-- ================= CATEGORY ================= -->
        <section class="poll-card mb-4">
            <div class="category-header">
                <div>
                    <div class="category-title">
                        KATEGORI
                        <?= e($indicator['category_no']) ?>
                    </div>

                    <h2>
                        <?= e($indicator['category']) ?>
                    </h2>

                    <p>
                        <?= e($indicator['focus']) ?>
                    </p>
                </div>

                <div class="category-icon">
                    <?php
                    $categoryIcons = [
                        1 => '🧠',
                        2 => '✨',
                        3 => '🤝',
                        4 => '🏆'
                    ];

                    echo $categoryIcons[
                        (int)$indicator['category_no']
                    ] ?? '📋';
                    ?>
                </div>
            </div>

            <!-- ================= TABLE ================= -->
            <div class="table-responsive">
                <table
                    class="table rating-table align-middle mb-0"
                >
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

        <!-- ================= INDIKATOR ================= -->
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

            <!-- ================= NILAI KANDIDAT ================= -->
            <?php foreach ($candidates as $candidate): ?>
                <td class="text-center">
                    <div class="rating-options">
                        <?php
                        for (
                            $score = 1;
                            $score <= 5;
                            $score++
                        ):
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

        <!-- ================================================= -->
        <!-- CATATAN TAMBAHAN / ESSAY                         -->
        <!-- ================================================= -->

        <section class="poll-card mb-4">
            <div class="category-header">
                <div>
                    <div class="category-title">
                        CATATAN TAMBAHAN / ESSAY (OPSIONAL)
                    </div>
                    <p class="mb-0">
                        <em>
                            Apresiasi atau masukan kualitatif
                            untuk kandidat.
                        </em>
                    </p>
                </div>

                <div class="category-icon">
                    📝
                </div>
            </div>

            <div class="mt-3">
                <p class="fw-semibold mb-3">
                    Tuliskan 1 hal mendasar yang paling menginspirasi Anda
                    dari kandidat terbaik dari pilihan anda:
                </p>

                <textarea
                    name="catatan_tambahan"
                    class="form-control"
                    rows="7"
                    maxlength="2000"
                    placeholder="Jawaban / Catatan Anda:"
                ></textarea>
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

            <!--
                PENTING:
                Tombol ini TIDAK memakai data-bs-toggle/data-bs-target lagi.
                Modal konfirmasi hanya boleh muncul SETELAH validasi JS
                (nama, NIP, semua rating) dinyatakan lengkap.
                Lihat script di bagian bawah: submitButton.addEventListener(...)
            -->
            <button
                type="button"
                id="openSubmitModalBtn"
                class="btn btn-primary btn-submit"
            >
                <i class="bi bi-send me-1"></i>
                Kirim Penilaian
            </button>
        </div>
    </form>

</main>

<!-- =====================================================
     MODAL KONFIRMASI
===================================================== -->
<div
    class="modal fade"
    id="confirmSubmitModal"
    tabindex="-1"
    aria-labelledby="confirmSubmitModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmSubmitModalLabel">
                    <i class="bi bi-question-circle text-primary me-1"></i>
                    Konfirmasi Pengiriman
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin seluruh penilaian sudah benar dan
                siap untuk dikirim? Data yang sudah terkirim tidak
                dapat diubah kembali.
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Batal
                </button>
                <button
                    type="button"
                    id="confirmSubmitBtn"
                    class="btn btn-primary"
                >
                    <i class="bi bi-send me-1"></i>
                    Ya, Kirim Penilaian
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================
     MODAL SUKSES
===================================================== -->
<div
    class="modal fade"
    id="successSubmitModal"
    tabindex="-1"
    aria-labelledby="successSubmitModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bi bi-check-circle-fill text-success" style="font-size:3rem;"></i>
                <h5 class="mt-3 mb-1" id="successSubmitModalLabel">
                    Penilaian Berhasil Dikirim
                </h5>
                <p class="text-muted mb-0">
                    Terima kasih atas partisipasi Anda dalam penilaian ini.
                </p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <a href="index.php" class="btn btn-primary">
                    Tutup
                </a>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================
     MODAL SUDAH PERNAH MENGISI (DUPLIKAT NIP)
===================================================== -->
<div
    class="modal fade"
    id="duplicateSubmitModal"
    tabindex="-1"
    aria-labelledby="duplicateSubmitModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-octagon-fill text-danger" style="font-size:3rem;"></i>
                <h5 class="mt-3 mb-1" id="duplicateSubmitModalLabel">
                    NIP Ini Sudah Pernah Mengisi Penilaian
                </h5>
                <p class="text-muted mb-0">
                    Setiap staf hanya dapat memberikan penilaian satu kali.
                    Jika Anda merasa ini keliru, silakan hubungi admin.
                </p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <a href="index.php" class="btn btn-outline-danger">
                    Tutup
                </a>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================
     BOOTSTRAP JS
===================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>



<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /*
        |--------------------------------------------------------------------------
        | ELEMENT
        |--------------------------------------------------------------------------
        */

        const form =
            document.getElementById(
                'pollingForm'
            );


        const submitButton =
            document.getElementById(
                'openSubmitModalBtn'
            );


        const confirmSubmitButton =
            document.getElementById(
                'confirmSubmitBtn'
            );


        const namaInput =
            document.querySelector(
                'input[name="nama_penilai"]'
            );


        const nipInput =
            document.querySelector(
                'input[name="nip"]'
            );


        const validationAlert =
            document.getElementById(
                'validationAlert'
            );


        const validationAlertText =
            document.getElementById(
                'validationAlertText'
            );



        /*
        |--------------------------------------------------------------------------
        | MODAL KONFIRMASI
        |--------------------------------------------------------------------------
        */

        const confirmModal =
            new bootstrap.Modal(
                document.getElementById(
                    'confirmSubmitModal'
                )
            );



        /*
        |--------------------------------------------------------------------------
        | MODAL SUKSES
        |--------------------------------------------------------------------------
        */

        const successModalElement =
            document.getElementById(
                'successSubmitModal'
            );


        const successModal =
            new bootstrap.Modal(
                successModalElement
            );



        /*
        |--------------------------------------------------------------------------
        | MODAL SUDAH PERNAH MENGISI (DUPLIKAT NIP)
        |--------------------------------------------------------------------------
        */

        const duplicateModalElement =
            document.getElementById(
                'duplicateSubmitModal'
            );


        const duplicateModal =
            new bootstrap.Modal(
                duplicateModalElement
            );



        /*
        |--------------------------------------------------------------------------
        | HILANGKAN ERROR NAMA SAAT DIISI
        |--------------------------------------------------------------------------
        */

        namaInput.addEventListener(
            'input',
            function () {

                if (
                    this.value.trim() !== ''
                ) {

                    this.classList.remove(
                        'input-error'
                    );

                }

                hideValidationAlertIfComplete();

            }
        );



        /*
        |--------------------------------------------------------------------------
        | HILANGKAN ERROR NIP SAAT DIISI
        |--------------------------------------------------------------------------
        */

        nipInput.addEventListener(
            'input',
            function () {

                if (
                    this.value.trim() !== ''
                ) {

                    this.classList.remove(
                        'input-error'
                    );

                }

                hideValidationAlertIfComplete();

            }
        );



        /*
        |--------------------------------------------------------------------------
        | HILANGKAN ERROR RATING SAAT DIPILIH
        |--------------------------------------------------------------------------
        */

        const radioButtons =
            document.querySelectorAll(
                'input[type="radio"][name^="nilai_"]'
            );


        radioButtons.forEach(
            function (radio) {

                radio.addEventListener(
                    'change',
                    function () {


                        const ratingContainer =
                            this.closest(
                                '.rating-options'
                            );


                        if (ratingContainer) {

                            ratingContainer.classList.remove(
                                'rating-error'
                            );

                        }


                        const row =
                            this.closest('tr');


                        if (row) {

                            row.classList.remove(
                                'indicator-error'
                            );

                        }


                        hideValidationAlertIfComplete();

                    }
                );

            }
        );



        /*
        |--------------------------------------------------------------------------
        | CEK APAKAH SEMUA SUDAH LENGKAP, LALU SEMBUNYIKAN ALERT
        |--------------------------------------------------------------------------
        */

        function hideValidationAlertIfComplete() {

            const stillHasError =
                document.querySelector(
                    '.input-error, .rating-error, tr.indicator-error'
                );

            if (!stillHasError) {

                validationAlert.classList.remove(
                    'show'
                );

            }

        }



        /*
        |--------------------------------------------------------------------------
        | KLIK KIRIM PENILAIAN
        |--------------------------------------------------------------------------
        */

        submitButton.addEventListener(
            'click',
            function () {


                let isValid = true;

                let firstError = null;

                let missingIdentityCount = 0;

                let missingRatingCount = 0;



                /*
                |--------------------------------------------------------------------------
                | CEK NAMA
                |--------------------------------------------------------------------------
                */

                if (
                    namaInput.value.trim() === ''
                ) {


                    namaInput.classList.add(
                        'input-error'
                    );


                    isValid = false;

                    missingIdentityCount++;

                    firstError =
                        namaInput;

                }



                /*
                |--------------------------------------------------------------------------
                | CEK NIP
                |--------------------------------------------------------------------------
                */

                if (
                    nipInput.value.trim() === ''
                ) {


                    nipInput.classList.add(
                        'input-error'
                    );


                    isValid = false;

                    missingIdentityCount++;


                    if (!firstError) {

                        firstError =
                            nipInput;

                    }

                }



                /*
                |--------------------------------------------------------------------------
                | AMBIL GROUP RATING
                |--------------------------------------------------------------------------
                */

                const ratingGroups =
                    new Set();


                document
                    .querySelectorAll(
                        'input[type="radio"][name^="nilai_"]'
                    )
                    .forEach(
                        function (radio) {

                            ratingGroups.add(
                                radio.name
                            );

                        }
                    );



                /*
                |--------------------------------------------------------------------------
                | CEK RATING
                |--------------------------------------------------------------------------
                */

                ratingGroups.forEach(
                    function (groupName) {


                        const selected =
                            document.querySelector(
                                'input[name="' +
                                groupName +
                                '"]:checked'
                            );


                        if (!selected) {


                            isValid = false;

                            missingRatingCount++;


                            const firstRadio =
                                document.querySelector(
                                    'input[name="' +
                                    groupName +
                                    '"]'
                                );


                            if (!firstRadio) {

                                return;

                            }


                            const ratingContainer =
                                firstRadio.closest(
                                    '.rating-options'
                                );


                            if (ratingContainer) {

                                ratingContainer.classList.add(
                                    'rating-error'
                                );

                            }


                            const row =
                                firstRadio.closest(
                                    'tr'
                                );


                            if (row) {

                                row.classList.add(
                                    'indicator-error'
                                );


                                if (!firstError) {

                                    firstError =
                                        row;

                                }

                            }

                        }

                    }
                );



                /*
                |--------------------------------------------------------------------------
                | JIKA ADA YANG BELUM DIISI
                |--------------------------------------------------------------------------
                */

                if (!isValid) {


                    let pesan = [];


                    if (missingIdentityCount > 0) {

                        pesan.push(
                            'Nama dan/atau NIP belum diisi'
                        );

                    }


                    if (missingRatingCount > 0) {

                        pesan.push(
                            missingRatingCount +
                            ' penilaian kandidat belum dipilih'
                        );

                    }


                    validationAlertText.textContent =
                        pesan.join(', ') +
                        '. Mohon lengkapi bagian yang bergaris merah di bawah ini.';


                    validationAlert.classList.add(
                        'show'
                    );


                    validationAlert.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });


                    if (firstError) {

                        setTimeout(
                            function () {

                                firstError.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                            },
                            400
                        );

                    }


                    return;

                }



                /*
                |--------------------------------------------------------------------------
                | SEMUA LENGKAP
                |--------------------------------------------------------------------------
                */

                validationAlert.classList.remove(
                    'show'
                );


                confirmModal.show();

            }
        );



        /*
        |--------------------------------------------------------------------------
        | KONFIRMASI KIRIM
        |--------------------------------------------------------------------------
        */

        confirmSubmitButton.addEventListener(
            'click',
            function () {


                /*
                |--------------------------------------------------------------------------
                | CEGAH KLIK DUA KALI
                |--------------------------------------------------------------------------
                */

                confirmSubmitButton.disabled =
                    true;


                confirmSubmitButton.innerHTML = `

                    <span
                        class="spinner-border spinner-border-sm me-1"
                        role="status"
                        aria-hidden="true"
                    ></span>

                    Mengirim...

                `;


                /*
                |--------------------------------------------------------------------------
                | SUBMIT FORM
                |--------------------------------------------------------------------------
                */

                form.submit();

            }
        );



        /*
        |--------------------------------------------------------------------------
        | CEK STATUS SUCCESS
        |--------------------------------------------------------------------------
        */

        const urlParams =
            new URLSearchParams(
                window.location.search
            );


        const status =
            urlParams.get('status');


        if (
            status === 'success'
        ) {


            /*
            |--------------------------------------------------------------------------
            | TAMPILKAN POPUP SUKSES
            |--------------------------------------------------------------------------
            */

            successModal.show();


            window.history.replaceState(
                {},
                document.title,
                window.location.pathname
            );

        } else if (
            status === 'duplicate'
        ) {


            /*
            |--------------------------------------------------------------------------
            | TAMPILKAN POPUP SUDAH PERNAH MENGISI
            |--------------------------------------------------------------------------
            */

            duplicateModal.show();


            window.history.replaceState(
                {},
                document.title,
                window.location.pathname
            );

        } else if (
            status === 'error'
        ) {


            /*
            |--------------------------------------------------------------------------
            | TAMPILKAN ALERT JIKA GAGAL DISIMPAN DI SERVER
            |--------------------------------------------------------------------------
            */

            validationAlertText.textContent =
                'Terjadi kesalahan saat menyimpan data. ' +
                'Silakan periksa kembali isian Anda dan coba kirim ulang.';

            validationAlert.classList.add(
                'show'
            );

            validationAlert.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            window.history.replaceState(
                {},
                document.title,
                window.location.pathname
            );

        }

    }
);
</script>

</body>
</html>