<?php
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate');
header('pragma: no-cache');
header('Expires: 0');


if(!isset($_SESSION['score'])){
    $_SESSION['score'] = 0;
}
if(!isset($_POST['password'])){
    header('Location: index.php');
    exit;
}

$password = $_POST['password'];

$python = 'py';
$script = __DIR__ . '/crack.py';
$cmd = $python . ' ' . escapeshellarg($script) . ' ' . escapeshellarg($password) . ' 2>&1';
$time = shell_exec($cmd);
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
    $time_text = round($time / 3600, 2) . "時間";
}
elseif($time < 31536000){
    $time_text = round($time / 86400, 2) . "日";
}
elseif($time < 3153600000){
    $time_text = round($time / 31536000, 2) . "年";
}
else{
    $time_text = "億年以上";
}

$is_strong = $time >= 3153600000;

if($is_strong && !isset($_SESSION['password_scored'])){
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

<div class="loading" id="loading">
    <p class="alert-text">⚠解析中⚠</p>
    <p class="loading-text">パスワードを解析しています...</p>
    <div class="progress-bar">
        <div class="progress-fill"></div>
    </div>
</div>
    
<div id="result" style="display:none;">

<?php if($is_strong): ?>

    <div class="story">
        <p class="alert-text">✓攻撃阻止成功✓</p>
        <h1 id="typing-text"></h1>
        <p>あなたのパスワードは<strong><?php echo $time_text; ?></strong>かかります。</p>
        <p>このパスワードは非常に強力です。</p>
        <a href="result.php"><button>結果を見る</button></a>
    </div>

<?php else: ?>

    <div class="story">
        <p class="alert-text">×まだ危険です×</p>
        <h1>パスワードが弱すぎます。</h1>
        <p>解析時間：<strong><?php echo $time_text; ?></strong></p>
        <p>もっと長く複雑なパスワードを作ってください。</p>
        <div class="cipher-box">
            <p>強いパスワードのポイント</p>
            <p>12文字以上にする</p>
            <p>英字・数字・記号を組み合わせる</p>
        </div>
        <a href="index.php?step=3"><button>もう一度試す</button></a>
    </div>

<?php endif; ?>

</div>

<script>
    window.onload = function(){
        setTimeout(function(){
            document.getElementById('loading').style.display = 'none';
            var result = document.getElementById('result');
            result.style.display = 'flex';
            result.style.flexDirection = 'column';
            result.style.alignItems = 'center';
            result.style.width = '100%';
            result.style.justifyContent = 'center';
        }, 3000);
    };
</script>

<script>
  const canvas = document.getElementById('matrix');
  const ctx = canvas.getContext('2d');

  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;

  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%&*';
  const fontSize = 14;
  const columns = canvas.width / fontSize;
  const drops = Array(Math.floor(columns)).fill(1);

  function drawMatrix(){
    ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#00ff41';
    ctx.font = fonSize + 'px Courier New';
    drops.forEach((y, i) =>{
      const char =chars[Math.floor(Math.random() * chars.length)];
      ctx.fillText(char, i * fontSize, y * fontSize);
      if (y * fontSize > canvas.height && Math.random() > 0.975){
        drops[i] = 0;
      }
      drops[i]++;
    });

  }

  setInterval(drawMatrix, 33);
</script>

<script>
    <?php if($is_strong): ?>
        const text ='ミッション完了！';
        const target = document.getELmentById('typing-text');
        let i = 0;

        function typing(){
            if (i < text.lenght){
                target.textContent +- text[i];
                i++;
                setTimeout(typing, 100);
            }
        }

        window.onload = function(){
            setTimeout(function(){
                document.getElementById('loading').style.display ='none';
                var result = document.getElementById('result');
                result.style.display = 'flex';
                result.style.flexDirection ='column';
                result.style.alignItems = 'center';
                result.style.width = '100%';
                result.style.justifyContent = 'center';
                typing();
            }, 3000);
        };
        <?php endif; ?>
        </script>
</body>
</html>

