<?php
require_once "config/database.php";

/*
|--------------------------------------------------------------------------
| Memastikan request berasal dari POST
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Mengambil data penilai
|--------------------------------------------------------------------------
*/
$nama = trim($_POST['nama_penilai'] ?? '');
$nip  = trim($_POST['nip'] ?? '');

/*
|--------------------------------------------------------------------------
| Mengambil catatan tambahan
|--------------------------------------------------------------------------
*/
$catatan_tambahan = trim($_POST['catatan_tambahan'] ?? '');

/*
|--------------------------------------------------------------------------
| Validasi nama dan NIP
|--------------------------------------------------------------------------
*/
if ($nama === '' || $nip === '') {
    die("Nama dan NIP/ID Pegawai wajib diisi.");
}

/*
|--------------------------------------------------------------------------
| Validasi panjang catatan
|--------------------------------------------------------------------------
*/
if (strlen($catatan_tambahan) > 2000) {
    die("Catatan tambahan terlalu panjang. Maksimal 2000 karakter.");
}
/*
|--------------------------------------------------------------------------
| Validasi apakah NIP sudah pernah memberikan penilaian
|--------------------------------------------------------------------------
*/
$check = $conn->prepare(
    "SELECT id FROM polls WHERE nip = ?"
);

$check->bind_param(
    "s",
    $nip
);

$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    die('
    <!doctype html>
    <html lang="id">
    <head>
        <meta charset="utf-8">
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
        >
        <title>Penilaian Sudah Tercatat</title>
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
    </head>
    <body class="notice-page">
        <div class="notice-wrapper">
            <div class="notice-card">
                <div class="notice-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h1>
                    Penilaian Sudah Tercatat
                </h1>
                <p>
                    NIP / ID Pegawai yang Anda masukkan
                    sudah pernah memberikan penilaian
                </p>
                <div class="notice-info">
                    <i class="bi bi-info-circle me-1"></i>
                    Setiap pegawai hanya dapat
                    memberikan penilaian satu kali
                </div>
                <a
                    href="index.php"
                    class="btn btn-primary px-4 py-2 notice-button"
                >
                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali ke Form
                </a>
                <div class="notice-brand">
                    Sistem Polling IST Kabupaten Madiun
                </div>
            </div>
        </div>
    </body>
    </html>
    ');
}

$check->close();

/*
|--------------------------------------------------------------------------
| Mengambil semua kandidat
|--------------------------------------------------------------------------
*/
$candidates = [];
$result = $conn->query(
    "SELECT id FROM candidates ORDER BY id"
);

while ($row = $result->fetch_assoc()) {
    $candidates[] = (int)$row['id'];
}


/*
|--------------------------------------------------------------------------
| Mengambil semua indikator
|--------------------------------------------------------------------------
*/
$indicators = [];
$result = $conn->query(
    "SELECT id FROM indicators ORDER BY id"
);

while ($row = $result->fetch_assoc()) {
    $indicators[] = (int)$row['id'];

}


/*
|--------------------------------------------------------------------------
| Mengambil semua nilai penilaian
|--------------------------------------------------------------------------
*/

$ratings = [];

foreach ($candidates as $candidateId) {
    foreach ($indicators as $indicatorId) {
        $key =
            "nilai_{$candidateId}_{$indicatorId}";
        $value = filter_input(
            INPUT_POST,
            $key,
            FILTER_VALIDATE_INT
        );

        /*
        |--------------------------------------------------------------
        | Setiap indikator wajib memiliki nilai 1 sampai 5
        |--------------------------------------------------------------
        */
       if (
    $value === false ||
    $value === null ||
    $value < 1 ||
    $value > 5
) {
    die(
        "Semua indikator wajib dinilai dengan skor 1 sampai 5."
    );
}


        $ratings[] = [
            $candidateId,
            $indicatorId,
            $value
        ];

    }

}


/*
|--------------------------------------------------------------------------
| Mulai transaksi database
|--------------------------------------------------------------------------
*/
$conn->begin_transaction();

try {
    /*
    |--------------------------------------------------------------------------
    | 1. MENYIMPAN IDENTITAS + CATATAN KE TABEL POLLS
    |--------------------------------------------------------------------------
    */
    $stmt = $conn->prepare("
        INSERT INTO polls
        (
            nama_penilai,
            nip,
            catatan_tambahan
        )
        VALUES
        ( ?, ?, ?
        )
    ");

    $stmt->bind_param(
        "sss",
        $nama,
        $nip,
        $catatan_tambahan
    );

    $stmt->execute();

    /*
    | ID poll yang baru dibuat
    */
    $pollId = $conn->insert_id;
    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | 2. MENIYIMPAN SEMUA NILAI KE TABEL RATINGS
    |--------------------------------------------------------------------------
    */
    $stmt = $conn->prepare("
        INSERT INTO ratings
        (
            poll_id,
            candidate_id,
            indicator_id,
            score
        )
        VALUES
        (?,?,?,?
        )
    ");

    foreach (
        $ratings
        as [$candidateId, $indicatorId, $score]
    ) {
        $stmt->bind_param(
            "iiii",
            $pollId,
            $candidateId,
            $indicatorId,
            $score
        );
        $stmt->execute();
    }

    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | 3. SEMUA BERHASIL
    |--------------------------------------------------------------------------
    */
    $conn->commit();

    /*
    |--------------------------------------------------------------------------
    | 4. Menuju ke halaman sukses
    |--------------------------------------------------------------------------
    */
    header(
        "Location: index.php"
    );

    exit;
} catch (Throwable $e) {

    /*
    |--------------------------------------------------------------------------
    | Jika ada error, batalkan semuanya
    |--------------------------------------------------------------------------
    */
    $conn->rollback();
    die(
        "Gagal menyimpan penilaian. Silakan coba lagi."
    );

}

?>