<?php
// =====================================================
// LOGOUT.PHP — Hapus Session & Redirect ke Login
// =====================================================

session_start();
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
?>
