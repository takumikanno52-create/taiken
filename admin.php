<?php
session_start();

$admin_password = 'SOUSAI';

// ログイン処理
if (isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin'] = true;
    } else {
        $error = 'パスワードが違います。';
    }
}

// ログアウト処理
if (isset($_GET['logout'])) {
    unset($_SESSION['admin']);
    header('Location: admin.php');
    exit;
}

// リセット処理
if (isset($_GET['reset']) && $_SESSION['admin']) {
    session_destroy();
    session_start();
    $message = '全セッションをリセットしました。';
}

// エラーログ取得
$error_log = '';
if (isset($_SESSION['admin']) && $_SESSION['admin']) {
    $log_file = 'C:/xampp/php/logs/php_error_log';
    if (file_exists($log_file)) {
        $lines = array_slice(file($log_file), -20);
        $error_log = implode('', $lines);
    } else {
        $error_log = 'エラーログが見つかりません。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>管理者画面</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<canvas id="matrix"></canvas>
<div class="scan-bar"></div>
<div class="corner corner-tl"></div>
<div class="corner corner-tr"></div>
<div class="corner corner-bl"></div>
<div class="corner corner-br"></div>

<div class="main">

<?php if (!isset($_SESSION['admin']) || !$_SESSION['admin']): ?>

  <div class="terminal">
    <div class="terminal-header">
      <div class="terminal-dot red"></div>
      <div class="terminal-dot yellow"></div>
      <div class="terminal-dot green"></div>
      <span class="terminal-title">ADMIN_LOGIN.exe</span>
    </div>
    <div class="terminal-body">
      <div class="agent-message">
        <div class="agent-line system">SYSTEM: 管理者認証が必要です。</div>
      </div>
    </div>
  </div>

  <?php if (isset($error)): ?>
    <p class="alert-text"><?php echo $error; ?></p>
  <?php endif; ?>

  <form method="POST" action="admin.php" autocomplete="off">
    <input type="password" name="password" placeholder="管理者パスワードを入力" autocomplete="off">
    <button type="submit">ログイン</button>
  </form>

<?php else: ?>

  <div class="terminal">
    <div class="terminal-header">
      <div class="terminal-dot red"></div>
      <div class="terminal-dot yellow"></div>
      <div class="terminal-dot green"></div>
      <span class="terminal-title">ADMIN_PANEL.exe</span>
    </div>
    <div class="terminal-body">
      <div class="agent-message">
        <div class="agent-line success">SYSTEM: 管理者としてログインしました。</div>
      </div>
    </div>
  </div>

  <?php if (isset($message)): ?>
    <p class="alert-text"><?php echo $message; ?></p>
  <?php endif; ?>

  <div class="admin-grid">
    <div class="admin-card">
  <p class="admin-card-title">🔓 暗号解読スキップ</p>
  <p>暗号解読をスキップしてパスワード強化ステージに進みます。<br>デモ・確認時に使用してください。</p>
  <a href="skip.php"><button>スキップする</button></a>
</div>

    <div class="admin-card">
      <p class="admin-card-title">🔄 セッションリセット</p>
      <p>全ユーザーのセッションをリセットします。<br>トラブル時や体験終了後に使用してください。</p>
      <a href="admin.php?reset=1"><button>リセットする</button></a>
    </div>

    <div class="admin-card">
      <p class="admin-card-title">🏠 体験トップに戻る</p>
      <p>体験のトップページに移動します。<br>動作確認に使用してください。</p>
      <a href="index.php"><button>トップに移動</button></a>
    </div>

    <div class="admin-card">
      <p class="admin-card-title">⚠️ 緊急停止</p>
      <p>体験を強制終了してトップページに戻します。<br>緊急時に使用してください。</p>
      <a href="index.php?step=1"><button class="danger-btn">緊急停止</button></a>
    </div>

    <div class="admin-card">
      <p class="admin-card-title">🚪 ログアウト</p>
      <p>管理者画面からログアウトします。</p>
      <a href="admin.php?logout=1"><button>ログアウト</button></a>
    </div>

  </div>

  <div class="terminal" style="margin-top: 20px;">
    <div class="terminal-header">
      <div class="terminal-dot red"></div>
      <div class="terminal-dot yellow"></div>
      <div class="terminal-dot green"></div>
      <span class="terminal-title">ERROR_LOG.exe</span>
    </div>
    <div class="terminal-body">
      <pre class="error-log"><?php echo htmlspecialchars($error_log); ?></pre>
    </div>
  </div>

  <?php endif; ?>

</div>

<script>
const canvas = document.getElementById('matrix');
const ctx = canvas.getContext('2d');
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;
const fontSize = 14;
const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*';
const columns = Math.floor(canvas.width / fontSize);
const drops = Array(columns).fill(1);
function drawMatrix() {
    ctx.fillStyle = 'rgba(0,0,0,0.05)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#00ff41';
    ctx.font = fontSize + 'px Courier New';
    drops.forEach((y, i) => {
        const char = chars[Math.floor(Math.random() * chars.length)];
        ctx.fillText(char, i * fontSize, y * fontSize);
        if (y * fontSize > canvas.height && Math.random() > 0.975) drops[i] = 0;
        drops[i]++;
    });
}
setInterval(drawMatrix, 33);
window.addEventListener('resize', () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
});
</script>
<script>
let skipInput = '';
document.addEventListener('keydown', function(e) {
    skipInput += e.key.toUpperCase();
    if (skipInput.includes('KANBU')) {
        window.location.href = 'skip.php';
    }
    if (skipInput.length > 10) {
        skipInput = skipInput.slice(-10);
    }
});
</script>
</body>
</html>