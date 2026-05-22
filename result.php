<?php
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate');
header('pragma: no-cache');
header('Expires: 0');


$score = isset($_SESSION['score']) ? $_SESSION['score'] : 0;

//スコアに応じたメッセージ
if ($score === 2){
    $message = "完璧です！セキュリティ意識が非常に高いです";
    $advice = "この調子でパスワード管理を続けましょう。";
}
elseif ($score === 1){
    $message = "もう少しです！セキュリティ意識は高めです。" ;
    $advice = "数字だけ・短いパスワードは今すぐに変更しましょう。";
}
else {
    $message = "危険です！パスワードを見直す必要があります";
    $advice = "英字・数字・記号を組み合わせた長いパスワードにしましょう。";
}


//セッションをリセット
session_destroy();?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>診断結果</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<canvas id="matrix"></canvas>
    
    <div class="story">
    <h1 id="typing-text"></h1>
    <p>あなたのスコア：<strong><?php echo $score; ?> / 2</strong></p>
    <p><?php echo $message; ?></p>
    <p><?php echo $advice; ?></p>

    <div class="advice-box">
        <p>ホワイトハッカーからのアドバイス</p>
        <p>パスワードは１２文字以上にする</p>
        <p>英字・数字・記号を組み合わせる</p>
        <p>同じパスワードを使い回さない</p>
        <p>定期的にパスワードを変更する</p>
    </div>

    <a href="index.php">
        <button>もう一度試す</button>
    </a>

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
    const text ='診断結果';
    const target = document.getElementById('typing-text');
    let i = 0;

    function typing(){
        if(i< text.lenght){
            target.textContent += text[i];
            i++;
            setTimeout(typing, 100);
        }
    }

    typing();
    </script>
</body>
</html>