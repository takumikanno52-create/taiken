<?php
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = 0;
}
if (!isset($_POST['password'])) {
    header('Location: index.php');
    exit;
}

$password = $_POST['password'];
$python = 'py';
$script = __DIR__ . '/crack.py';
$cmd = $python . ' ' . escapeshellarg($script) . ' ' . escapeshellarg($password) . ' 2>&1';
$time = shell_exec($cmd);
$time = floatval(trim($time));

if ($time < 0.001) {
    $time_text = "0.001秒以下";
} elseif ($time < 1) {
    $time_text = round($time, 6) . "秒";
} elseif ($time < 60) {
    $time_text = round($time, 2) . "秒";
} elseif ($time < 3600) {
    $time_text = round($time / 60, 2) . "分";
} elseif ($time < 86400) {
    $time_text = round($time / 3600, 2) . "時間";
} elseif ($time < 31536000) {
    $time_text = round($time / 86400, 2) . "日";
} elseif ($time < 3153600000) {
    $time_text = round($time / 31536000, 2) . "年";
} else {
    $time_text = "億年以上";
}

$is_strong = $time >= 3153600000;

if ($is_strong && !isset($_SESSION['password_scored'])) {
    $_SESSION['score'] += 1;
    $_SESSION['password_scored'] = true;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>暗号解読ミッション</title>
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
  <div class="terminal" id="analyzing-terminal">
    <div class="terminal-header">
      <div class="terminal-dot red"></div>
      <div class="terminal-dot yellow"></div>
      <div class="terminal-dot green"></div>
      <span class="terminal-title">PASSWORD_ANALYZER.exe</span>
    </div>
    <div class="terminal-body" id="analyze-output">
    </div>
  </div>
</div>

<div class="main" id="result-screen" style="display:none;">

<?php if ($is_strong): ?>
  <div class="story">
    <p class="alert-text">✓ 攻撃阻止成功 ✓</p>
    <h1 id="typing-text"></h1>
    <p>あなたのパスワードは<strong><?php echo $time_text; ?></strong>かかります。</p>
    <p>このパスワードは非常に強力です。</p>
    <a href="result.php"><button>結果を見る</button></a>
  </div>
<?php else: ?>
  <div class="story">
    <p class="alert-text">× まだ危険です ×</p>
    <h1>パスワードが弱すぎます。</h1>
    <p>解析時間：<strong><?php echo $time_text; ?></strong></p>
    <p>もっと長く複雑なパスワードを作ってください。</p>
    <div class="cipher-box">
      <p>強いパスワードのポイント</p>
      <p>・12文字以上にする</p>
      <p>・英字・数字・記号を組み合わせる</p>
    </div>
    <a href="index.php?step=3"><button>もう一度試す</button></a>
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
const isStrong = <?php echo $is_strong ? 'true' : 'false'; ?>;
const output = document.getElementById('analyze-output');

function addLine(text, className, delay) {
    setTimeout(() => {
        const line = document.createElement('div');
        line.className = 'cmd-line' + (className ? ' ' + className : '');
        line.textContent = text;
        line.style.opacity = '0';
        line.style.transition = 'opacity 0.3s';
        output.appendChild(line);
        setTimeout(() => { line.style.opacity = '1'; }, 50);
    }, delay);
}

function animateCount(id, target, duration, suffix) {
    setTimeout(() => {
        const el = document.getElementById(id);
        const start = performance.now();
        function update(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            el.textContent = Math.floor(progress * target) + suffix;
            if (progress < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
    }, 0);
}

addLine('> Initializing password strength test...', '', 200);
addLine('> Scanning character composition...', '', 700);

setTimeout(() => {
    const bar1 = document.createElement('div');
    bar1.className = 'progress-item';
    bar1.innerHTML = '<span class="progress-label">文字種類の解析</span>'
        + '<div class="progress-bar-wrapper">'
        + '<div class="progress-bar-fill" id="pfill1"></div>'
        + '</div>'
        + '<span class="progress-percent" id="ppct1">0%</span>';
    output.appendChild(bar1);
    setTimeout(() => {
        document.getElementById('pfill1').style.width = '100%';
        animateCount('ppct1', 100, 1000, '%');
    }, 100);
}, 1200);

addLine('> Calculating entropy level...', '', 2400);

setTimeout(() => {
    const bar2 = document.createElement('div');
    bar2.className = 'progress-item';
    bar2.innerHTML = '<span class="progress-label">エントロピー計算</span>'
        + '<div class="progress-bar-wrapper">'
        + '<div class="progress-bar-fill warning" id="pfill2"></div>'
        + '</div>'
        + '<span class="progress-percent" id="ppct2">0%</span>';
    output.appendChild(bar2);
    setTimeout(() => {
        document.getElementById('pfill2').style.width = '100%';
        animateCount('ppct2', 100, 1000, '%');
    }, 100);
}, 2800);

addLine('> Running brute force simulation...', '', 4000);

setTimeout(() => {
    const bar3 = document.createElement('div');
    bar3.className = 'progress-item';
    bar3.innerHTML = '<span class="progress-label">解析時間の推定</span>'
        + '<div class="progress-bar-wrapper">'
        + '<div class="progress-bar-fill error" id="pfill3"></div>'
        + '</div>'
        + '<span class="progress-percent" id="ppct3">0%</span>';
    output.appendChild(bar3);
    setTimeout(() => {
        document.getElementById('pfill3').style.width = '100%';
        animateCount('ppct3', 100, 1000, '%');
    }, 100);
}, 4400);

if (isStrong) {
    addLine('> Analysis complete.', 'success', 5600);
    addLine('> RESULT: PASSWORD IS STRONG', 'success', 6100);
} else {
    addLine('> Analysis complete.', 'success', 5600);
    addLine('> RESULT: PASSWORD IS VULNERABLE', 'error', 6100);
}

// 結果画面に切り替え
setTimeout(() => {
    document.getElementById('analyzing-terminal').style.transition = 'opacity 1s';
    document.getElementById('analyzing-terminal').style.opacity = '0';
    setTimeout(() => {
        document.querySelector('.main').style.display = 'none';
        const result = document.getElementById('result-screen');
        result.style.display = 'flex';
        result.style.opacity = '0';
        result.style.transition = 'opacity 0.8s';
        setTimeout(() => { result.style.opacity = '1'; }, 100);

        <?php if ($is_strong): ?>
        const text = 'ミッション完了！';
        const target = document.getElementById('typing-text');
        let i = 0;
        function typing() {
            if (i < text.length) {
                target.textContent += text[i];
                i++;
                setTimeout(typing, 120);
            }
        }
        typing();
        <?php endif; ?>
    }, 1000);
}, 6800);
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