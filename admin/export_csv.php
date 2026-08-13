<?php

require_once "../config/database.php";
require_once "../config/helpers.php";

require_admin();

/*
|--------------------------------------------------------------------------
| EXPORT DATA POLLING KE CSV
|--------------------------------------------------------------------------
|
| Format:
| 1 baris = 1 penilai + 1 kandidat
|
| Kolom:
| - Informasi penilaian
| - Nilai BRAIN
| - Nilai BEAUTY
| - Total skor
| - Rata-rata skor
|
| CSV menggunakan delimiter ";" agar langsung terbaca sebagai kolom
| oleh Microsoft Excel pada pengaturan regional Indonesia.
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Nama file
|--------------------------------------------------------------------------
*/

$filename = "hasil_polling_" . date("Y-m-d_H-i-s") . ".csv";


/*
|--------------------------------------------------------------------------
| Ambil semua indikator
|--------------------------------------------------------------------------
|
| Diurutkan berdasarkan nomor kategori dan ID indikator.
| Dengan begitu urutannya tetap:
|
| BRAIN
|   1.1
|   1.2
|   1.3
|
| BEAUTY
|   2.1
|   2.2
|   2.3
|--------------------------------------------------------------------------
*/

$indicators = [];

$result = $conn->query("
    SELECT
        id,
        category_no,
        category,
        code,
        name
    FROM indicators
    ORDER BY category_no ASC, id ASC
");

while ($row = $result->fetch_assoc()) {
    $indicators[] = $row;
}


/*
|--------------------------------------------------------------------------
| Ambil seluruh nilai polling
|--------------------------------------------------------------------------
|
| Relasi:
|
| polls
|   -> ratings
|       -> candidates
|       -> indicators
|
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        p.id AS poll_id,
        p.nama_penilai,
        p.nip,
        p.submitted_at,

        c.id AS candidate_id,
        c.code AS candidate_code,
        c.name AS candidate_name,

        i.id AS indicator_id,
        i.category_no,
        i.category,
        i.code AS indicator_code,
        i.name AS indicator_name,

        r.score

    FROM ratings r

    INNER JOIN polls p
        ON p.id = r.poll_id

    INNER JOIN candidates c
        ON c.id = r.candidate_id

    INNER JOIN indicators i
        ON i.id = r.indicator_id

    ORDER BY
        p.id ASC,
        c.id ASC,
        i.category_no ASC,
        i.id ASC
";

$result = $conn->query($sql);


/*
|--------------------------------------------------------------------------
| Kelompokkan data
|--------------------------------------------------------------------------
|
| Satu baris CSV:
|
| 1 penilai + 1 kandidat
|
|--------------------------------------------------------------------------
*/

$data = [];

while ($row = $result->fetch_assoc()) {

    $key = $row['poll_id'] . "_" . $row['candidate_id'];

    if (!isset($data[$key])) {

        $data[$key] = [
            'poll_id' => $row['poll_id'],
            'nama_penilai' => $row['nama_penilai'],
            'nip' => $row['nip'],
            'submitted_at' => $row['submitted_at'],
            'candidate_code' => $row['candidate_code'],
            'candidate_name' => $row['candidate_name'],
            'scores' => []
        ];

    }

    $data[$key]['scores'][(int) $row['indicator_id']] = (int) $row['score'];
}


/*
|--------------------------------------------------------------------------
| Header HTTP
|--------------------------------------------------------------------------
*/

header("Content-Type: text/csv; charset=UTF-8");

header(
    'Content-Disposition: attachment; filename="' .
    $filename .
    '"'
);

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");


/*
|--------------------------------------------------------------------------
| Output CSV
|--------------------------------------------------------------------------
*/

$output = fopen("php://output", "w");


/*
|--------------------------------------------------------------------------
| UTF-8 BOM
|--------------------------------------------------------------------------
|
| Membantu Excel membaca karakter Indonesia dengan benar.
|--------------------------------------------------------------------------
*/

fprintf($output, "\xEF\xBB\xBF");


/*
|--------------------------------------------------------------------------
| HEADER CSV
|--------------------------------------------------------------------------
|
| Nama kolom dibuat jelas supaya mudah digunakan di Excel.
|--------------------------------------------------------------------------
*/

$header = [
    "ID Polling",
    "Nama Penilai",
    "NIP / ID Pegawai",
    "Waktu Penilaian",
    "Kode Kandidat",
    "Nama Kandidat"
];


/*
|--------------------------------------------------------------------------
| Kolom nilai setiap indikator
|--------------------------------------------------------------------------
|
| Contoh:
| BRAIN - 1.1 Kompetensi Teknis
| BRAIN - 1.2 Inovasi & Inisiatif
| BRAIN - 1.3 Problem Solving
| BEAUTY - 2.1 Komunikasi Efektif
| ...
|--------------------------------------------------------------------------
*/

foreach ($indicators as $indicator) {

    $header[] =
        $indicator["category"] .
        " - " .
        $indicator["code"] .
        " " .
        $indicator["name"];

}


/*
|--------------------------------------------------------------------------
| Kolom tambahan hasil perhitungan sederhana
|--------------------------------------------------------------------------
|
| Ini bukan menggantikan perhitungan pemenang di dashboard.
| Hanya membantu ketika data dibuka di Excel.
|--------------------------------------------------------------------------
*/

$header[] = "Total Skor";
$header[] = "Rata-rata Skor";


/*
|--------------------------------------------------------------------------
| Tulis header
|--------------------------------------------------------------------------
|
| Delimiter ";" digunakan agar Excel Indonesia otomatis memisahkan
| data menjadi beberapa kolom.
|--------------------------------------------------------------------------
*/

fputcsv($output, $header, ";");


/*
|--------------------------------------------------------------------------
| Tulis data
|--------------------------------------------------------------------------
*/

foreach ($data as $row) {

    $csvRow = [
        $row["poll_id"],
        $row["nama_penilai"],
        $row["nip"],
        $row["submitted_at"],
        $row["candidate_code"],
        $row["candidate_name"]
    ];

    $totalScore = 0;
    $scoreCount = 0;


    /*
    |----------------------------------------------------------------------
    | Nilai setiap indikator
    |----------------------------------------------------------------------
    */

    foreach ($indicators as $indicator) {

        $indicatorId = (int) $indicator["id"];

        if (isset($row["scores"][$indicatorId])) {

            $score = (int) $row["scores"][$indicatorId];

            $csvRow[] = $score;

            $totalScore += $score;
            $scoreCount++;

        } else {

            /*
            | Jika nilai belum tersedia, biarkan kosong.
            */

            $csvRow[] = "";

        }

    }


    /*
    |----------------------------------------------------------------------
    | Total dan rata-rata
    |----------------------------------------------------------------------
    */

    $averageScore = $scoreCount > 0
        ? round($totalScore / $scoreCount, 2)
        : 0;


    $csvRow[] = $totalScore;
    $csvRow[] = number_format(
        $averageScore,
        2,
        ".",
        ""
    );


    /*
    |----------------------------------------------------------------------
    | Tulis baris
    |----------------------------------------------------------------------
    */

    fputcsv($output, $csvRow, ";");
}


fclose($output);

exit;
?>
