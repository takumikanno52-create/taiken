<?php 
header('Cache-Control: no-store, no-cache, must-revalidate');
header('pragma: no-cache');
header('Expires: 0');

$password = 'HACKER';

$python = 'py';
$script = __DIR__ . '/crack.py';
$time = shell_exec($python . ' ' . escapeshellarg($script) . ' ' . escapeshellarg($password) . ' 2>&1');
$time = floatval(trim($time));

if($time < 0.001){
    $time_text = "0.001秒以下";
}
elseif($time < 1){
    $time_text = round($time, 6) . "秒";
}
elseif($time < 60){
    $time_text = round($time, 2) . "秒";
}
elseif($time < 3600){
    $time_text = round($time / 60, 2) . "分";
}
elseif($time < 86400){
    $time_text = round ($time / 3600, 2) . "時間";
}
elseif($time < 315360000){
    $time_text = round($time / 86400, 2) . "日";
}
elseif($time < 3153600000){
    $time_text = round($time / 31536000, 2) . "年";
}
else{
    $time_text = "億年以上";
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

    <div class="loading" id="loading">
        <p class="alert-text">⚠解析中⚠</p>
        <p class="loading-text">パスワードを解析しています...</p>
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
    </div>
    
    <div class="story" id="result" style="display:none;">
        <p class="alert-text">⚠解析結果⚠</p>
        <h1 id="typing-text"></h1>
        <p>このパスワードは</p>
        <p class="cipher-text"><?php echo $time_text; ?></p>
        <p>で破られます</p>
        <p>このパスワードは非常に危険です。<br>では、あなたは強いパスワードを作れますか？</p>
        <a href="index.php?step=3">
            <button>強いパスワードを作る</button>
        </a>
    </div>

    <script>
        setTimeout(function(){
            document.getElementById('loading').style.display = 'none';
            document.getElementById('result').style.display = 'flex';
         }, 3000);
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
    const text= '犯人のパスワード「HACKER」';
    const target = document.getElementById('typing-text');
    let i = 0;

    function typing(){
        if (i < text.lenght){
            target.textContent += text[i];
            i++;
            setTimeout(typing, 100);
        }
    }

    setTimeout(function(){
        document.getElementById('loading').style.display ='none';
        var result = document.getElementById('result');
        result.style.display ='flex';
        result.style.flexDirection ='column';
        result.style.alignItems ='center';
        result.style.width = '100%';
        result.style.justifyContent ='center';
        typing();
    }, 3000);
    </script>

</body>
</html>
