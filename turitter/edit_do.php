<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Turitter</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php
    error_reporting(E_ALL & ~E_NOTICE);
    session_start();
    require('dbconnect.php');
    
    if(isset($_SESSION['id'])) {
      $id = $_REQUEST['id'];

      $message = $pdo -> prepare('SELECT * FROM member_posts WHERE id=?');
      $message -> execute(array($id));
      $message = $message -> fetch();

      if($message['member_id'] === $_SESSION['id']) {
        $edit = $pdo -> prepare('UPDATE member_posts SET message=? WHERE id=?');
        $edit -> execute(array(
          $_POST['message'],
          $id
        )); //Warning: PDOStatement::execute(): SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'WHERE id='92'' at line 1 in /public_html/programming/edit_do.php on line 27は案外そこの間違いじゃなくて、'UPDATE member_posts SET message=? WHERE id=?'で余分に","を入れていることが多い!!'UPDATE member_posts SET message=?, WHERE id=?' こんな感じ
      }
    }
    echo 'メッセージが編集されました。';
  ?>
  <div style="text-align: left"><a href="index.php">一覧に戻る</a></div>
</body>
</html>