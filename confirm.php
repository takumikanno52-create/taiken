<?php 
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate');
header('pragma: no-cache');
header('Expires: 0');


if(!isset($_POST['password'])){
    header('Location: index.php');
    exit;
}

$password = $_POST['password'];
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
    
    <div class="cipher-box">
        <h1>確認してください。</h1>
        <p>入力されたパスワード:</p>
        <p class="cipher-text"></p echo htmlspecialchars(password); ?></p>
        <p>これはどこかで使っているパスワードではないですか？</p>
    </div>

    <form method="POST" action="analyze2.php">
        <input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">
        <button type="submit" name="choice" value="safe">架空のものです→診断する</button>
    </form>

    <form method="POST" action="index.php?step=3">
        <button type="submit">使っています→入力し直す</button>
    </form>

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
</script>>

</body>
</html>