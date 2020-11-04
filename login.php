<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start(); // コレを忘れるとうまく作動しないよ
require('dbconnect.php');

if($_COOKIE['email'] !== '') {
  $email = $_COOKIE['email'];
}

if($_POST) {
  $email = $_POST['email'];
  if ($_POST['email'] !== '' && $_POST['password'] !== '') {
    $login = $pdo -> prepare("SELECT*FROM members WHERE email=? AND password=?");
    $login -> execute(array(
      $_POST['email'],
      sha1($_POST['password'])
    ));
    $member = $login -> fetch();
    if($member) {
      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();

      if($_POST['save'] === 'on') {
        setcookie('email',$_POST['email'],time()+60*60*24*14);
      }
      header('location: index.php');
      exit();
    }else {
      $error['login'] ='failed';
    }
  }else {
    $error['login'] = 'blank';
  }
}
  
?>

<!DOCTYPE html>
<html lag="ja">
<head>
  <meta charset="UTF-8" />
  <link rel="stylesheet" type="text/css" href="style.css" />
  <title>ログインする</title>
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ログインする</h1>
  </div>
  <div id="content">
    <div id="lead">
      <p>メールアドレスとパスワードを記入してログインしてください。</p>
      <p>入会手続きがまだの方はこちらからどうぞ。</p>
      <p>&raquo;<a href="join/">入会手続きをする</a></p>
    </div>
    <form action="" method="post">
      <dl>
        <dt>メールアドレス</dt>
        <dd>
          <input type="text" name="email" size="35" maxlength="255" value="<?= htmlspecialchars($email,ENT_QUOTES); ?>" />
          <?php if($error['login'] === 'blank') :?>
					<p class="error">*メールアドレスとパスワードを入れて下さい </p>
					<?php endif; ?>
          <?php if($error['login'] === 'failed') :?>
					<p class="error">*ログインに失敗しました。正しく入れて下さい</p>
					<?php endif; ?>
        </dd>
        <dt>パスワード</dt>
        <dd>
          <input type="password" name="password" size="35" maxlength="255" value="<?= htmlspecialchars($_POST['password'],ENT_QUOTES); ?>" />
        </dd>
        <dt>ログイン情報の記録</dt>
        <dd>
          <input id="save" type="checkbox" name="save" value="on">
          <label for="save">次回からは自動的にログインする</label>
        </dd>
      </dl>
      <div>
        <input type="submit" value="ログインする" />
      </div>
    </form>
  </div>
  <div id="foot">
    <p><img src="images/txt_copyright.png"width='10%' height='10%' alt="(C)turitter.com" /></p>
  </div>
</div>
</body>
</html>
