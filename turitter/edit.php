<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require('dbconnect.php');

if(isset($_SESSION['id'])) { //membersテーブルのidカラムのことである コレは直接入られたときに防止するため
  $id = $_REQUEST['id'];

  $message = $pdo -> prepare('SELECT m.name, m.picture, p.* FROM members m, member_posts p WHERE m.id=p.member_id AND p.id=?');
  $message -> execute(array($id));
  $message = $message -> fetch();

  // if($message['member_id'] === $_SESSION['id']) {
  //   $del = $pdo -> prepare('DELETE FROM member_posts WHERE id=?');
  //   $del -> execute(array($id));
  // }
}

// header('location: index.php');
// exit();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Turitter.com</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div id="post">
    <div id="head">
      <h6>Turitter.com</h6>
      <h1>釣りバカ日誌</h1>
    </div>
    <div style="text-align: left"><a href="index.php">一覧に戻る</a></div>
    <form action="edit_do.php?id=<?= htmlspecialchars($message['id'],ENT_QUOTES);?>" method="post"  enctype="multipart/form-data">
      <dl>
        <dt><?= htmlspecialchars($message['name'],ENT_QUOTES) ?>さん、メッセージを編集して下さい</dt>
        <dd>
          <textarea name="message" cols="50" rows="5"><?= htmlspecialchars($message['message'],ENT_QUOTES); ?></textarea>
          <input type="hidden" name="id" value="<?= $id; ?>">
        </dd>
        <dd>
          <!-- <input type="file" name="postimage" /> -->
          <?php //if($error['image'] === 'type'): ?>
          <!-- <p class="error">*写真などは「.gif」または「.jpg」または「.png」の画像を指定して下さい </p> -->
          <?php //endif; ?>
          <br>
          <br>
          <?php if($message['post_picture'] !== NULL): ?>
            <a href="member_post_picture/<?= htmlspecialchars($message['post_picture'],ENT_QUOTES); ?>" target="_blank"><img src="member_post_picture/<?= htmlspecialchars($message['post_picture'],ENT_QUOTES); ?>" width="20%" height="20%" alt="<?= htmlspecialchars($message['post_picture'],ENT_QUOTES); ?>"></a>
            <h6>＊現在保存されている写真です。</h6>
          <?php endif; ?>
        </dd>
      </dl>
      <div>
        <p>
          <input type="submit" value="変更する" />
        </p>
      </div>
    </form>
  </div>
</body>
</html>