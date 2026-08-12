<?php
require_once "../config/database.php";
session_start();

if (!empty($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password_hash FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: index.php");
        exit;
    }
    $error = 'Username atau password salah.';
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login Admin - Polling IST</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>
        body {
            min-height: 100vh;
            background: #f4f6fb;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            max-width: 950px;
            width: 100%;
            border: none;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .08);
        }

        /* Bagian kiri */
        .login-brand {
            background: linear-gradient(145deg, #3157d5, #203da0);
            color: white;
            min-height: 520px;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-logo {
            width: 75px;
            height: 75px;
            background: rgba(255, 255, 255, .15);
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 20px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 22px;
            font-weight: 800;

            margin-bottom: 30px;
        }

        .login-brand h1 {
            font-weight: 800;
            font-size: 32px;
            margin-bottom: 15px;
        }

        .login-brand p {
            color: rgba(255, 255, 255, .8);
            line-height: 1.7;
            max-width: 400px;
        }

        .brand-info {
            margin-top: 30px;
        }

        .brand-info div {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, .9);
        }

        /* Bagian kanan */
        .login-form {
            background: white;
            min-height: 520px;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form h2 {
            font-weight: 800;
            color: #172033;
            margin-bottom: 8px;
        }

        .login-subtitle {
            color: #6c757d;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #343a40;
        }

        .input-group-text {
            background: #f8f9fc;
            border-right: none;
            color: #6c757d;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 .2rem rgba(49, 87, 213, .12);
            border-radius: 8px;
        }

        .btn-login {
            background: #3157d5;
            border-color: #3157d5;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            transition: .2s;
        }

        .btn-login:hover {
            background: #2648bc;
            border-color: #2648bc;
            transform: translateY(-1px);
        }

        .back-link {
            color: #3157d5;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .login-footer {
            font-size: 13px;
            color: #98a2b3;
            text-align: center;
            margin-top: 30px;
        }

        @media (max-width: 767px) {

            .login-brand {
                min-height: auto;
                padding: 35px;
            }

            .login-brand h1 {
                font-size: 26px;
            }

            .login-form {
                min-height: auto;
                padding: 35px;
            }

            .brand-info {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="login-wrapper">

    <div class="card login-card">

        <div class="row g-0">

            <!-- LEFT -->
            <div class="col-lg-6">

                <div class="login-brand">

                    <div class="brand-logo">
                        IST
                    </div>

                    <h1>
                        Seleksi Insan<br>
                        Statistik Teladan
                    </h1>

                    <p>
                        Sistem pengelolaan dan rekapitulasi
                        penilaian pegawai Kabupaten Madiun.
                    </p>

                    <div class="brand-info">

                        <div>
                            <i class="bi bi-shield-check"></i>
                            <span>Akses khusus administrator</span>
                        </div>

                        <div>
                            <i class="bi bi-bar-chart-line"></i>
                            <span>Monitoring hasil penilaian</span>
                        </div>

                        <div>
                            <i class="bi bi-people"></i>
                            <span>Rekapitulasi data polling</span>
                        </div>

                    </div>

                </div>

            </div>


            <!-- RIGHT -->
            <div class="col-lg-6">

                <div class="login-form">

                    <div class="text-center mb-2">

                        <div class="d-inline-flex
                                    align-items-center
                                    justify-content-center
                                    bg-primary-subtle
                                    text-primary
                                    rounded-circle"
                            style="width:60px;height:60px;">

                            <i class="bi bi-person-lock fs-3"></i>

                        </div>

                    </div>

                    <h2 class="text-center">
                        Selamat Datang
                    </h2>

                    <p class="login-subtitle text-center">
                        Silakan masuk ke halaman administrator.
                    </p>


                    <!-- ERROR -->
                    <?php if ($error): ?>

                        <div class="alert alert-danger d-flex align-items-center"
                            role="alert">

                            <i class="bi bi-exclamation-circle-fill me-2"></i>

                            <div>
                                <?= htmlspecialchars($error) ?>
                            </div>

                        </div>

                    <?php endif; ?>


                    <form method="post">

                        <!-- USERNAME -->
                        <div class="mb-3">

                            <label class="form-label">
                                Username
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>

                                <input
                                    type="text"
                                    name="username"
                                    class="form-control form-control-lg"
                                    placeholder="Masukkan username"
                                    autocomplete="username"
                                    required
                                >

                            </div>

                        </div>


                        <!-- PASSWORD -->
                        <div class="mb-4">

                            <label class="form-label">
                                Password
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>

                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control form-control-lg"
                                    placeholder="Masukkan password"
                                    autocomplete="current-password"
                                    required
                                >

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    id="togglePassword"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>

                            </div>

                        </div>


                        <!-- LOGIN -->
                        <button
                            type="submit"
                            class="btn btn-login btn-primary btn-lg w-100"
                        >

                            <i class="bi bi-box-arrow-in-right me-2"></i>

                            Masuk ke Dashboard

                        </button>

                    </form>


                    <!-- BACK -->
                    <div class="text-center mt-4">

                        <a
                            href="../index.php"
                            class="back-link"
                        >

                            <i class="bi bi-arrow-left me-1"></i>

                            Kembali ke Form Polling

                        </a>

                    </div>


                    <div class="login-footer">

                        Sistem Polling IST Kabupaten Madiun
                        &copy; <?= date('Y') ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

const password = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");

togglePassword.addEventListener("click", function () {

    const icon = this.querySelector("i");

    if (password.type === "password") {

        password.type = "text";

        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");

    } else {

        password.type = "password";

        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");

    }

});

</script>

</body>
</html>
