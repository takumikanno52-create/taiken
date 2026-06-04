<?php
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if(!isset($_POST['answer'])){
    header('Location: index.php');
    exit;
}

$answer = strtoupper(trim($_POST['answer']));
$correct = $_SESSION['correct'];

if(!isset($_SESSION['score'])){
    $_SESSION['score'] = 0;
}

if($answer === $correct){
    if(isset($_SESSION['hint_used']) && $_SESSION['hint_used'] === true){
        $_SESSION['score'] += 0.5;
    }
    else{
        $_SESSION['score'] += 1;
    }
}

// 管理者コマンド判定
if ($answer === 'SOUSAI') {
    header('Location: admin.php');
    exit;
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
    
<?php if ($answer === $correct): ?>

    <div class="story">
        <p class="alert-text">✓解読成功</p>
        <h1 id="typing-text"></h1>
        <p>パスワード:<strong><?php echo $correct; ?></strong></p>
        <p>しかし、このパスワードは<br>どれくらいで破られるのでしょうか？</p>
        <a href="analyze_hacker.php">
            <button>解析する</button>
        </a>
    </div>

<?php else: ?>

    <div class="story">
        <p class="alert-text">×解析失敗×</p>
        <h1 id="typing-text"></h1>
        <p>もう一度挑戦してください。</p>
        <a href="index.php?step=2">
            <button>もう一度試す</button>
        </a>
    </div>

<?php endif; ?>

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
    ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#00ff41';
    ctx.font = fontSize + 'px Courier New';
    drops.forEach((y, i) => {
        const char = chars[Math.floor(Math.random() * chars.length)];
        ctx.fillText(char, i * fontSize, y * fontSize);
        if (y * fontSize > canvas.height && Math.random() > 0.975) {
            drops[i] = 0;
        }
        drops[i]++;
    });
}

setInterval(drawMatrix, 33);
</script>

<script>
    const isCorrect = <?php echo ($answer === $correct) ? 'true' : 'false'; ?>;
    const text =isCorrect ? '犯人のパスワードが判明しました。' : '暗号解読に失敗しました。';
    const targer = document.getElementById('typing-text');
    let i = 0;
    
    function typing(){
        if(i < text.length){
            target.textContent += text[i];
            i++ ;
            setTimeout(typing, 100);
        }
    }

    typing();

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