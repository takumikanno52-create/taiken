<?php
session_start();
if (!isset($_GET['step']) || $_GET['step'] == 1) {
    $_SESSION['score'] = 0;
    unset($_SESSION['password_scored']);
    unset($_SESSION['correct']);
    unset($_SESSION['hint_used']);
}
$step = isset($_GET['step']) ? $_GET['step'] : 1;
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

<?php if ($step == 1): ?>

  <p class="alert-text">⚠ WARNING ⚠</p>
  <h1 id="typing-text"></h1>
  <p>所要時間：約5〜10分　　対象：どなたでも参加できます</p>
  <p>＊これはペネトレーションテストの一環です。＊</p>
  <button id="start-btn" onclick="startMission()">体験を始める</button>

  <div id="breach-screen" style="display:none; width:100%; flex-direction:column; align-items:center; gap:20px;">
    <div class="breach-overlay" id="breach-overlay"></div>
    <div class="emergency-text" id="emergency-text">⚠ 緊急事態発生 ⚠</div>

    <div class="terminal" id="main-terminal">
      <div class="terminal-header">
        <div class="terminal-dot red"></div>
        <div class="terminal-dot yellow"></div>
        <div class="terminal-dot green"></div>
        <span class="terminal-title">SECURITY_SYSTEM.exe</span>
      </div>
      <div class="terminal-body" id="terminal-output">
      </div>
    </div>

    <div class="terminal" id="breach-terminal" style="display:none;">
      <div class="terminal-header">
        <div class="terminal-dot red"></div>
        <div class="terminal-dot yellow"></div>
        <div class="terminal-dot green"></div>
        <span class="terminal-title">INCOMING_MESSAGE.exe</span>
      </div>
      <div class="terminal-body" id="agent-output">
      </div>
    </div>

    <div id="mission-btn-wrapper" style="opacity:0; margin-top:20px;">
      <a href="index.php?step=2"><button>ミッション開始</button></a>
    </div>
  </div>



<?php elseif ($step == 2): ?>
<?php
$words = [
    'HACK', 'VIRUS', 'ATTACK', 'DEFEND', 'ACCESS',
    'CIPHER', 'BREACH', 'SYSTEM', 'SECURE', 'THREAT',
    'DETECT', 'DECODE', 'NETWORK', 'ENCRYPT', 'MALWARE',
    'PHISHING', 'TROJAN', 'EXPLOIT', 'PAYLOAD', 'BYPASS',
    'STEALTH', 'FIREWALL', 'MONITOR', 'SCANNER', 'PACKET',
    'PROXY', 'TUNNEL', 'BOTNET', 'ROOTKIT', 'SPYWARE',
    'KEYLOG', 'SOCIAL', 'COOKIE', 'SESSION', 'TOKEN',
    'BACKUP', 'RESTORE', 'ALERT', 'BLOCK', 'FILTER',
    'PATCH', 'UPDATE', 'AUDIT', 'TRACE', 'INJECT',
    'SHELL', 'BINARY', 'SCRIPT', 'BUFFER', 'OVERFLOW'
];

// ランダムに1つ選ぶ
$word = $words[array_rand($words)];

// シーザー暗号で3つずらして暗号化する
$cipher = '';
for ($i = 0; $i < strlen($word); $i++) {
    $c = ord($word[$i]);
    $cipher .= chr((($c - 65 + 3) % 26) + 65);
}

$_SESSION['correct'] = $word;
?>

  <div class="terminal">
    <div class="terminal-header">
      <div class="terminal-dot red"></div>
      <div class="terminal-dot yellow"></div>
      <div class="terminal-dot green"></div>
      <span class="terminal-title">DECRYPTION_TOOL.exe</span>
    </div>
    <div class="terminal-body">
      <div class="agent-message">
        <div class="agent-line" style="animation-delay:0.2s">[AGENT_01]: 暗号文を見つけた。解読してくれ。</div>
        <div class="agent-line" style="animation-delay:1.0s">[AGENT_01]: アルファベットを3つ前にずらすと解読できるはずだ。</div>
      </div>
      <div class="cipher-box">
        <p class="cipher-text"><?php echo $cipher; ?></p>
        <button type="button" id="hint-btn" onclick="showHint()">ヒントを見る</button>
        <div id="hint" style="display:none;">
          <p>ヒント：アルファベットを3つ前にずらすと解読できます。</p>
          <p>例：D → A、E → B、F → C</p>
        </div>
        <button type="button" id="table-btn" onclick="showTable()">アルファベット表を見る</button>
        <div id="alpha-table" style="display:none;">
          <table>
            <tr>
              <th>アルファベット</th>
              <td>A</td><td>B</td><td>C</td><td>D</td><td>E</td>
              <td>F</td><td>G</td><td>H</td><td>I</td><td>J</td>
              <td>K</td><td>L</td><td>M</td><td>N</td><td>O</td>
              <td>P</td><td>Q</td><td>R</td><td>S</td><td>T</td>
              <td>U</td><td>V</td><td>W</td><td>X</td><td>Y</td><td>Z</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <form method="POST" action="check.php" autocomplete="off">
    <input type="text" name="answer" placeholder="解読した文字を入力してください" maxlength="10" autocomplete="off">
    <button type="submit">解読する</button>
  </form>

<?php elseif ($step == 3): ?>

  <div class="terminal">
    <div class="terminal-header">
      <div class="terminal-dot red"></div>
      <div class="terminal-dot yellow"></div>
      <div class="terminal-dot green"></div>
      <span class="terminal-title">PASSWORD_GENERATOR.exe</span>
    </div>
    <div class="terminal-body">
      <div class="agent-message">
        <div class="agent-line" style="animation-delay:0.2s">[AGENT_01]: よくやった！次はパスワードを強化してくれ。</div>
        <div class="agent-line" style="animation-delay:1.0s">[AGENT_01]: 強いパスワードを作って攻撃を完全に阻止するんだ。</div>
        <div class="agent-line" style="animation-delay:1.8s">[AGENT_01]: ヒントを参考に、架空のパスワードを作ってくれ。</div>
      </div>
      <div class="cipher-box">
        <p>・好きな食べ物（例：pizza）</p>
        <p>・好きな数字（例：0714）</p>
        <p>・好きな記号（例：@）</p>
        <p>組み合わせ例：pizza0714@</p>
      </div>
    </div>
  </div>

  <p class="warning-text">⚠ 普段使っているパスワードは絶対に入力しないでください ⚠</p>

  <form method="POST" action="confirm.php" autocomplete="off">
    <input type="text" name="password" placeholder="架空のパスワードを入力してください" maxlength="30" autocomplete="off">
    <button type="submit">確認する</button>
  </form>

<?php endif; ?>

</div>

<script>
function showHint() {
    document.getElementById('hint').style.display = 'block';
    document.getElementById('hint-btn').style.display = 'none';
    fetch('hint.php');
}

function showTable() {
    document.getElementById('alpha-table').style.display = 'block';
    document.getElementById('table-btn').style.display = 'none';
}
</script>

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

<?php if ($step == 1): ?>
<script>
const text = '暗号解読ミッション';
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

function startMission() {
    document.getElementById('start-btn').style.display = 'none';
    document.getElementById('typing-text').style.display = 'none';
    document.querySelectorAll('.main > p').forEach(el => el.style.display = 'none');
    document.getElementById('breach-screen').style.display = 'flex';

    const output = document.getElementById('terminal-output');

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

    // フェーズ1：スキャン
    addLine('> Initializing security scan...', '', 200);
    addLine('> Scanning network packets...', '', 700);
    addLine('> WARNING: Suspicious activity detected', 'warning', 1200);
    addLine('> Analyzing threat level...', '', 1700);
    addLine('> CRITICAL: Unauthorized access in progress', 'error', 2200);
    addLine('> BREACH DETECTED', 'error', 2700);

    // フェーズ2：進捗バー
    setTimeout(() => {
        const bar1 = document.createElement('div');
        bar1.className = 'progress-item';
        bar1.innerHTML = '<span class="progress-label">ネットワーク解析</span>'
    + '<div class="progress-bar-wrapper">'
    + '<div class="progress-bar-fill" id="fill1"></div>'
    + '</div>'
    + '<span class="progress-percent" id="pct1">0%</span>';
        output.appendChild(bar1);
        setTimeout(() => {
            document.getElementById('fill1').style.width = '100%';
            animatePercent('pct1', 100, 1000);
        }, 100);
    }, 3200);

    setTimeout(() => {
        const bar2 = document.createElement('div');
        bar2.className = 'progress-item';
       bar2.innerHTML = '<span class="progress-label">脅威レベル判定</span>'
    + '<div class="progress-bar-wrapper">'
    + '<div class="progress-bar-fill warning" id="fill2"></div>'
    + '</div>'
    + '<span class="progress-percent" id="pct2">0%</span>';
        output.appendChild(bar2);
        setTimeout(() => {
            document.getElementById('fill2').style.width = '85%';
            animatePercent('pct2', 85, 1000);
        }, 100);
    }, 3700);

    setTimeout(() => {
        const bar3 = document.createElement('div');
        bar3.className = 'progress-item';
       bar3.innerHTML = '<span class="progress-label">侵入経路特定</span>'
    + '<div class="progress-bar-wrapper">'
    + '<div class="progress-bar-fill error" id="fill3"></div>'
    + '</div>'
    + '<span class="progress-percent" id="pct3">0%</span>';
        output.appendChild(bar3);
        setTimeout(() => {
            document.getElementById('fill3').style.width = '100%';
            animatePercent('pct3', 100, 1000);
        }, 100);
    }, 4200);

    // フェーズ3：IPトレース
    const ip1 = `192.168.${Math.floor(Math.random()*255)}.${Math.floor(Math.random()*255)}`;
    addLine('> TRACING ORIGIN... ' + ip1, 'error', 5400);
    addLine('> LOCATION: UNKNOWN', 'error', 5900);
    addLine('> THREAT LEVEL: CRITICAL', 'error', 6400);
    addLine('> COUNTERMEASURES REQUIRED IMMEDIATELY', 'error', 6900);

    // ターミナルをフェードアウト
    setTimeout(() => {
        const terminal = document.getElementById('main-terminal');
        terminal.style.transition = 'opacity 1.5s';
        terminal.style.opacity = '0';
    }, 7500);

    // フェードアウトと同時に緊急事態発生を表示
    setTimeout(() => {
        document.getElementById('breach-overlay').classList.add('active');
        document.getElementById('emergency-text').classList.add('active');
    }, 7500);

    // ターミナルを非表示にして会話ターミナルに切り替え
    setTimeout(() => {
        document.getElementById('main-terminal').style.display = 'none';
        const agentTerminal = document.getElementById('breach-terminal');
        agentTerminal.style.display = 'block';
        agentTerminal.style.opacity = '0';
        agentTerminal.style.transition = 'opacity 0.8s';
        setTimeout(() => {
            agentTerminal.style.opacity = '1';
        }, 100);
    }, 11500);

    // AGENT_01会話
    const agentOutput = document.getElementById('agent-output');

    function addAgentLine(text, className, delay) {
        setTimeout(() => {
            const line = document.createElement('div');
            line.className = 'agent-line' + (className ? ' ' + className : '');
            line.textContent = text;
            line.style.opacity = '0';
            line.style.transition = 'opacity 0.3s';
            agentOutput.appendChild(line);
            setTimeout(() => { line.style.opacity = '1'; }, 50);
        }, delay);
    }

    addAgentLine('SYSTEM: 侵入者を検知しました。', 'system', 12000);
    addAgentLine('SYSTEM: 企業の機密データが危険にさらされています。', 'system', 12800);
    addAgentLine('[AGENT_01]: 緊急支援要請。聞こえるか？', '', 14000);
    addAgentLine('[AGENT_01]: 犯人が暗号を残していった。解読を頼む。', '', 14800);
    addAgentLine('[AGENT_01]: 時間がない。今すぐ頼む！', 'success', 15600);

    // ミッション開始ボタン
    setTimeout(() => {
        const btn = document.getElementById('mission-btn-wrapper');
        btn.style.opacity = '1';
        btn.style.transition = 'opacity 0.5s';
    }, 16400);
}

function animatePercent(id, target, duration) {
    const el = document.getElementById(id);
    const start = performance.now();
    function update(now) {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);
        el.textContent = Math.floor(progress * target) + '%';
        if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
}
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
<?php endif; ?>

</body>
</html>