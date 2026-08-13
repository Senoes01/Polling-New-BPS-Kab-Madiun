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
    header("Location: index.php?status=error");
    exit;
}


/*
|--------------------------------------------------------------------------
| Validasi panjang nama
|--------------------------------------------------------------------------
*/
if (strlen($nama) > 120) {
    header("Location: index.php?status=error");
    exit;
}


/*
|--------------------------------------------------------------------------
| Validasi panjang NIP
|--------------------------------------------------------------------------
*/
if (strlen($nip) > 50) {
    header("Location: index.php?status=error");
    exit;
}


/*
|--------------------------------------------------------------------------
| Validasi panjang catatan
|--------------------------------------------------------------------------
*/
if (strlen($catatan_tambahan) > 2000) {
    header("Location: index.php?status=error");
    exit;
}


/*
|--------------------------------------------------------------------------
| Validasi apakah NIP sudah pernah memberikan penilaian
|--------------------------------------------------------------------------
*/
$check = $conn->prepare(
    "SELECT id FROM polls WHERE nip = ? LIMIT 1"
);

if (!$check) {
    header("Location: index.php?status=error");
    exit;
}

$check->bind_param(
    "s",
    $nip
);

$check->execute();
$check->store_result();


/*
|--------------------------------------------------------------------------
| Jika NIP sudah pernah mengisi
|--------------------------------------------------------------------------
*/
if ($check->num_rows > 0) {

    $check->close();

    /*
    |--------------------------------------------------------------------------
    | Kembali ke index.php dan tampilkan modal duplicate
    |--------------------------------------------------------------------------
    */
    header("Location: index.php?status=duplicate");
    exit;
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

if (!$result) {
    header("Location: index.php?status=error");
    exit;
}

while ($row = $result->fetch_assoc()) {
    $candidates[] = (int) $row['id'];
}

$result->free();


/*
|--------------------------------------------------------------------------
| Mengambil semua indikator
|--------------------------------------------------------------------------
*/
$indicators = [];

$result = $conn->query(
    "SELECT id FROM indicators ORDER BY id"
);

if (!$result) {
    header("Location: index.php?status=error");
    exit;
}

while ($row = $result->fetch_assoc()) {
    $indicators[] = (int) $row['id'];
}

$result->free();


/*
|--------------------------------------------------------------------------
| Pastikan kandidat dan indikator tersedia
|--------------------------------------------------------------------------
*/
if (empty($candidates) || empty($indicators)) {
    header("Location: index.php?status=error");
    exit;
}


/*
|--------------------------------------------------------------------------
| Mengambil semua nilai penilaian
|--------------------------------------------------------------------------
*/
$ratings = [];

foreach ($candidates as $candidateId) {

    foreach ($indicators as $indicatorId) {

        /*
        |--------------------------------------------------------------------------
        | Nama field sesuai dengan index.php
        |--------------------------------------------------------------------------
        |
        | Contoh:
        | nilai_1_1
        | nilai_1_2
        | nilai_2_1
        |
        |--------------------------------------------------------------------------
        */
        $key = "nilai_{$candidateId}_{$indicatorId}";


        /*
        |--------------------------------------------------------------------------
        | Ambil nilai POST
        |--------------------------------------------------------------------------
        */
        $rawValue = $_POST[$key] ?? null;


        /*
        |--------------------------------------------------------------------------
        | Validasi nilai
        |--------------------------------------------------------------------------
        */
        if (
            $rawValue === null ||
            !is_scalar($rawValue) ||
            !ctype_digit((string) $rawValue)
        ) {
            header("Location: index.php?status=error");
            exit;
        }


        $value = (int) $rawValue;


        /*
        |--------------------------------------------------------------------------
        | Nilai harus 1 sampai 5
        |--------------------------------------------------------------------------
        */
        if ($value < 1 || $value > 5) {
            header("Location: index.php?status=error");
            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Simpan ke array ratings
        |--------------------------------------------------------------------------
        */
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
        (?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception(
            "Gagal menyiapkan query polls."
        );
    }


    $stmt->bind_param(
        "sss",
        $nama,
        $nip,
        $catatan_tambahan
    );


    /*
    |--------------------------------------------------------------------------
    | Eksekusi INSERT polls
    |--------------------------------------------------------------------------
    */
    if (!$stmt->execute()) {
        throw new Exception(
            "Gagal menyimpan data penilai."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ID poll yang baru dibuat
    |--------------------------------------------------------------------------
    */
    $pollId = $conn->insert_id;


    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | 2. MENYIMPAN SEMUA NILAI KE TABEL RATINGS
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
        (?, ?, ?, ?)
    ");


    if (!$stmt) {
        throw new Exception(
            "Gagal menyiapkan query ratings."
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Simpan setiap nilai
    |--------------------------------------------------------------------------
    */
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


        if (!$stmt->execute()) {
            throw new Exception(
                "Gagal menyimpan nilai penilaian."
            );
        }
    }


    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | 3. SEMUA DATA BERHASIL DISIMPAN
    |--------------------------------------------------------------------------
    */
    $conn->commit();


    /*
    |--------------------------------------------------------------------------
    | 4. KEMBALI KE INDEX DAN TAMPILKAN MODAL SUKSES
    |--------------------------------------------------------------------------
    */
    header(
        "Location: index.php?status=success"
    );

    exit;


} catch (Throwable $e) {

    /*
    |--------------------------------------------------------------------------
    | Jika terjadi error, batalkan semua perubahan
    |--------------------------------------------------------------------------
    */
    $conn->rollback();


    /*
    |--------------------------------------------------------------------------
    | Kembali ke index dan tampilkan pesan error
    |--------------------------------------------------------------------------
    */
    header(
        "Location: index.php?status=error"
    );

    exit;
}

?>