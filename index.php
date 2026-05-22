<?php
session_start();
if(!isset($_GET['step']) || $_GET['step'] == 1){
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
<?php if ($step == 1): ?>

  <div class="story">
    <p class="alert-text">⚠ WARNING ⚠</p>
    <h1 class="typing">企業のシステムに不正アクセスが発生しました。</h1>
    <p>犯人が残した暗号を解読して<br>攻撃を阻止せよ。</p>
    <h4>  ⚠これはペネトレーションテストの一環です。⚠</h4>
    <a href="index.php?step=2"><button>ミッション開始</button></a>
  </div>

<?php elseif ($step == 2): ?>

<?php
$problems = [
   ['cipher' => 'KDFNHU', 'answer' => 'HACKER',  'meaning' => 'ハッカー'],
    ['cipher' => 'YLUXV',  'answer' => 'VIRUS',   'meaning' => 'ウイルス'],
    ['cipher' => 'DWWDFN', 'answer' => 'ATTACK',  'meaning' => '攻撃'],
    ['cipher' => 'GHIHQG', 'answer' => 'DEFEND',  'meaning' => '防御'],
    ['cipher' => 'DFFHVV', 'answer' => 'ACCESS',  'meaning' => 'アクセス'],
    ['cipher' => 'FLSKHU', 'answer' => 'CIPHER',  'meaning' => '暗号'],
];

$index = array_rand($problems);
$problem = $problems[$index];
$_SESSION['correct'] = $problem['answer'];
?>

  <h1>ステージ1：暗号を解読せよ</h1>
  <p>犯人が残した暗号文を解読してください。</p>

  <div class="cipher-box">
    <p class="cipher-text"><?php echo $problem['cipher']; ?></p>
    <button type="button" id="hint-btn" onclick="showHint()">ヒントを見る</button>
      <div id="hint" style="display:none;">
    <p>ヒント：アルファベットを3つ前にずらすと解読できます。</p>
    <p>例：D → A、E → B、F → C</p>
    </div>
    <button type="button" id="table-btn" onclick="showTable()">アルファベット表を見る</button>
    <div id="alpha-table" style="display:none;">
      <table>
        <tr>
          <th>暗号</th>
          <td>D</td><td>E</td><td>F</td><td>G</td><td>H</td>
          <td>I</td><td>J</td><td>K</td><td>L</td><td>M</td>
          <td>N</td><td>O</td><td>P</td><td>Q</td><td>R</td>
          <td>S</td><td>T</td><td>U</td><td>V</td><td>W</td>
          <td>x</td><td>Y</td><td>Z</td><td>A</td><td>B</td><td>C</td>
        </tr>
        <tr>
           <th>文字</th>
            <td>A</td><td>B</td><td>C</td><td>D</td><td>E</td>
            <td>F</td><td>G</td><td>H</td><td>I</td><td>J</td>
            <td>K</td><td>L</td><td>M</td><td>N</td><td>O</td>
            <td>P</td><td>Q</td><td>R</td><td>S</td><td>T</td>
            <td>U</td><td>V</td><td>W</td><td>X</td><td>Y</td><td>Z</td>
        </tr>
      </table>
    </div>
  </div>

  <form method="POST" action="check.php" autocomplete="off">
    <input type="text" name="answer" placeholder="答えを入力してください" maxlength="10" autocomplete="off">
    <button type="submit">解読する</button>
  </form>

<?php elseif ($step == 3): ?>

  <h1>ステージ2：強いパスワードを作れ</h1>
  <p>以下のヒントをもとに架空のパスワードを作ってください。</p>

  <div class="cipher-box">
    <p>・好きな食べ物（例：pizza）</p>
    <p>・好きな数字（例：0714）</p>
    <p>・好きな記号（例：@）</p>
    <p>組み合わせ例：pizza0714@</p>
  </div>

  <p class="warning-text">⚠ 普段使っているパスワードは絶対に入力しないでください ⚠</p>

  <form method="POST" action="confirm.php" autocomplete="off">
    <input type="text" name="password" placeholder="架空のパスワードを入力してください" maxlength="30" autocomplete="off">
    <button type="submit">確認する</button>
  </form>

<?php endif; ?>

<script>
  function showHint(){
    document.getElementById('hint').style.display = 'block';
    document.getElementById('hint-btn').style.display = 'none';
    fetch('hint.php');
  }
  function showTable(){
    document.getElementById('alpha-table').style.display ='block';
    document.getElementById('table-btn').style.display='none';
  }
</script>

<script>
  const canvas = document.getElementById('matrix');
  const ctx = canvas.getContext('2d');

  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;

  const fontSize = 14;
  const chars = 'ABCDEFGHIJKLMNOPKRSTUVWXYZ0123456789@#$%*';
  const columns = Math.floor(canvas.width / fontSize);
  const drops = Array(columns).fill(1);

  function drawMatrix(){
    ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#00ff41';
    ctx.font = fontSize + 'px Courier New';
    drops.forEach((y, i) =>{
      const char = chars[Math.floor(Math.random() * chars.length)];
      ctx.fillText(char, i * fontSize, y * fontSize);
      if(y * fontSize > canvas.height && Math.random() > 0.975){
        drops[i] = 0;
      }
      drops[i]++;
    })
  }
  setInterval(drawMatrix, 33);
</script>
</body>
</html>