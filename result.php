<?php
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$score = isset($_SESSION['score']) ? intval($_SESSION['score']) : 0;

if ($score >= 2) {
    $mission_status = 'SUCCESS';
    $alert_class = 'success';
    $alert_text = '✓ ミッション完了 ✓';
    $title = 'システムを守りきった！';
    $message = '完璧です！セキュリティ意識が非常に高いです。';
    $advice = 'この調子でパスワード管理を続けましょう。';
    $agent_msg = '見事だ。おかげでシステムを守れた。本当にありがとう。君はホワイトハッカーの素質がある。';
} elseif ($score >= 1) {
    $mission_status = 'PARTIAL';
    $alert_class = 'warning';
    $alert_text = '△ ミッション一部完了 △';
    $title = 'もう少しだった...';
    $message = '片方は成功しました。セキュリティ意識は高めです。';
    $advice = '英字・数字・記号を組み合わせた長いパスワードにしましょう。';
    $agent_msg = 'よくやってくれた。でも油断は禁物だ。次はもっと強いパスワードを作れるはずだ。';
} else {
    $mission_status = 'FAILED';
    $alert_class = 'error';
    $alert_text = '× ミッション失敗 ×';
    $title = 'システムが危険にさらされている...';
    $message = '危険です！パスワードをすぐに見直してください。';
    $advice = 'パスワードマネージャーの使用を検討しましょう。';
    $agent_msg = '今回は厳しかったな...でも諦めるな。もう一度挑戦してくれ。頼んだ。';
}

session_destroy();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ミッション結果</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<canvas id="matrix"></canvas>
<div class="scan-bar"></div>
<div class="corner corner-tl"></div>
<div class="corner corner-tr"></div>
<div class="corner corner-bl"></div>
<div class="corner corner-br"></div>

<div class="main" id="result-main" style="opacity:0;">

  <p class="alert-text"><?php echo $alert_text; ?></p>
  <h1 id="typing-text"></h1>

  <div class="terminal">
    <div class="terminal-header">
      <div class="terminal-dot red"></div>
      <div class="terminal-dot yellow"></div>
      <div class="terminal-dot green"></div>
      <span class="terminal-title">MISSION_REPORT.exe</span>
    </div>
    <div class="terminal-body">
      <div class="agent-message">
        <div class="agent-line <?php echo $alert_class; ?>" id="score-line" style="opacity:0;">
          MISSION STATUS: <?php echo $mission_status; ?>
        </div>
        <div class="agent-line" id="score-detail" style="opacity:0;">
          SCORE: <?php echo $score; ?> / 2
        </div>
        <div class="agent-line" id="msg-line" style="opacity:0;">
          <?php echo $message; ?>
        </div>
        <div class="agent-line" id="advice-line" style="opacity:0;">
          <?php echo $advice; ?>
        </div>
        <div class="agent-line" id="agent-line" style="opacity:0;">
          [AGENT_01]: <?php echo $agent_msg; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="advice-box">
    <p>ホワイトハッカーからのアドバイス</p>
    <p>・パスワードは12文字以上にする</p>
    <p>・英字・数字・記号を組み合わせる</p>
    <p>・同じパスワードを使い回さない</p>
    <p>・定期的にパスワードを変更する</p>
  </div>

  <a href="index.php"><button>もう一度挑戦する</button></a>

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
window.onload = function() {
    // ページをフェードイン
    const main = document.getElementById('result-main');
    main.style.transition = 'opacity 1s';
    setTimeout(() => { main.style.opacity = '1'; }, 300);

    // タイピング演出
    const text = '<?php echo $title; ?>';
    const target = document.getElementById('typing-text');
    let i = 0;
    function typing() {
        if (i < text.length) {
            target.textContent += text[i];
            i++;
            setTimeout(typing, 100);
        }
    }
    setTimeout(typing, 800);

    // 各行を順番に表示
    const lines = ['score-line', 'score-detail', 'msg-line', 'advice-line', 'agent-line'];
    const delays = [1500, 2200, 3000, 3800, 4800];
    lines.forEach((id, index) => {
        setTimeout(() => {
            const el = document.getElementById(id);
            el.style.opacity = '1';
            el.style.transition = 'opacity 0.3s';
        }, delays[index]);
    });
};
</script>

<script>
let adminInput = '';
document.addEventListener('keydown', function(e) {
    adminInput += e.key.toUpperCase();
    if (adminInput.includes('SOUSAI')) {
        window.location.href = 'admin.php';
    }
    if (adminInput.length > 10) {
        adminInput = adminInput.slice(-10);
    }
});
</script>

</body>
</html>