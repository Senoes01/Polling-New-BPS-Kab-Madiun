<?php
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function require_admin() {
    session_start();
    if (empty($_SESSION['admin_logged_in'])) {
        redirect("login.php");
    }
}
?>
