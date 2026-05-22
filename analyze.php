<?php
session_start();

if(!isset($_POST['password'])){
    header('Location: index.php');
    exit;
}

$password = $_POST['password'];
$step = $_POST['step'];

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
    $time_text = round($time / 3600, 2) . "時間";
}
else{
    $time_text = round($time / 86400, 2) . "日";    
}

//スコアの初期化
if(!isset($_SESSION['score'])){
    $_SESSION['score'] = 0;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード診断</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<canvas id="matrix"></canvas>
    
<?php if(!isset($_POST['choice'])): ?>

    <p>選ばれたパスワード：<strong?><?php echo htmlspecialchars($password); ?></strong></p>
    <p>解析時間：<strong><?php echo $time_text; ?></strong></p>
    <p>このパスワード、安全だと思いますか？</p>
    <form method="POST" action="analyze.php">
        <input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">
        <input type="hidden" name="step" value="<?php echo $step; ?>">
        <button type="submit" name="choice" value="safe">安全</button>
        <button type="submit" name="choice" value="danger">危険</button>
    </form>

<?php else: ?>

    <?php
    $choice = $_POST['choice'];

    //正解判定とスコア加算
    if ($time < 1 && $choice === 'danger'){
        $_SESSION['score'] += 1;
        $result_text = "正解！このパスワードは非常に危険です。";
    }
    elseif ($time >= 1 && $choice === 'safe'){
        $_SESSION['score'] += 1;
        $result_text = "正解！このパスワードは比較的安全です";
    }
    else{
        $result_text = "残念！答えは逆でした。";
    }

    //次の画面へ
    $next_step = $step + 1;
    ?>

    <p>選ばれたパスワード：<strong><?php echo htmlspecialchars($password); ?></strong></p>
    <p>このパスワードは<strong><?php echo $time_text; ?></strong>で破られます。</p>
    <p><?php echo $result_text; ?></p>

<?php if ($next_step <= 4): ?>
    <a href="index.php?step=<?php echo $next_step; ?>">
        <button>次の診断へ</button>
    </a>

<?php else: ?>
    <a href="result.php">
        <button>結果を見る</button>
    </a>

<?php endif; ?>


<?php endif; ?>

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
    
    </body>
</html>