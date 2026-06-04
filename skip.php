<?php
session_start();

// 管理者のみアクセス可能
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: admin.php');
    exit;
}

// スコアを1点加算してステージ3へ
if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = 0;
}
$_SESSION['score'] += 1;
$_SESSION['correct'] = 'SKIP';

header('Location: analyze_hacker.php');
exit;
?>