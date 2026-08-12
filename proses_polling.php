<?php
require_once "config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$nama = trim($_POST['nama_penilai'] ?? '');
$nip  = trim($_POST['nip'] ?? '');

if ($nama === '' || $nip === '') {
    die("Nama dan NIP/ID Pegawai wajib diisi.");
}

$check = $conn->prepare("SELECT id FROM polls WHERE nip = ?");
$check->bind_param("s", $nip);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    die('<div style="font-family:Arial;padding:40px;text-align:center">
        <h2>Penilaian Sudah Tercatat</h2>
        <p>NIP/ID Pegawai tersebut sudah pernah mengirim penilaian.</p>
        <a href="index.php">Kembali ke Form</a>
    </div>');
}
$check->close();

$candidates = [];
$result = $conn->query("SELECT id FROM candidates ORDER BY id");
while ($row = $result->fetch_assoc()) $candidates[] = (int)$row['id'];

$indicators = [];
$result = $conn->query("SELECT id FROM indicators ORDER BY id");
while ($row = $result->fetch_assoc()) $indicators[] = (int)$row['id'];

$ratings = [];
foreach ($candidates as $candidateId) {
    foreach ($indicators as $indicatorId) {
        $key = "nilai_{$candidateId}_{$indicatorId}";
        $value = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);
        if ($value === false || $value < 1 || $value > 5) {
            die("Semua indikator wajib dinilai dengan skor 1 sampai 5.");
        }
        $ratings[] = [$candidateId, $indicatorId, $value];
    }
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO polls (nama_penilai, nip) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama, $nip);
    $stmt->execute();
    $pollId = $conn->insert_id;
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO ratings (poll_id, candidate_id, indicator_id, score) VALUES (?, ?, ?, ?)");
    foreach ($ratings as [$candidateId, $indicatorId, $score]) {
        $stmt->bind_param("iiii", $pollId, $candidateId, $indicatorId, $score);
        $stmt->execute();
    }
    $stmt->close();

    $conn->commit();
    header("Location: sukses.php");
    exit;
} catch (Throwable $e) {
    $conn->rollback();
    die("Gagal menyimpan penilaian. Silakan coba lagi.");
}
?>
