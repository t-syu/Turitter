<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require('dbconnect.php');

if(empty($_REQUEST['id'])) {
  header('location: index,php');
  exit();
}

$member_posts = $pdo -> prepare('SELECT m.name, m.picture, p.* FROM members m, member_posts p WHERE m.id=p.member_id AND p.id=?');
$member_posts -> execute(array($_REQUEST['id'])); //executeは ? へ順番にぶち込む
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
<div id="wrap">
  <div id="head">
    <h6>Turitter.com</h6>
    <h1>釣りバカ日誌</h1>
  </div>
  <div id="content">
  <p>&laquo;<a href="index.php">一覧にもどる</a></p>

<?php if($post = $member_posts -> fetch()):?>
    <div class="msg">
<?php if($post['picture'] !== NULL): ?>>
    <img src="member_picture/<?= htmlspecialchars($post['picture']);?>" width="10%" height="10%" alt="<?= htmlspecialchars($post['name'],ENT_QUOTES);?>"/>
<?php else: ?>
    <img src="member_picture/nopictureuser.png" width='5%' height='5%' alt="nopictureuser.png">
<?php endif; ?>
    <p><?= htmlspecialchars($post['message']);?><span class="name">（<?= htmlspecialchars($post['name']);?>）</span></p>
    <p class="day"><?= htmlspecialchars($post['created']);?></p>
    </div>
<?php if($post['post_picture'] !== NULL): ?> <!-- NULLと''は違う意味をもつ -->
  <div class='picture'>
    <a href="member_post_picture/<?= htmlspecialchars($post['post_picture'],ENT_QUOTES); ?>" target="_blank"><img src="member_post_picture/<?= htmlspecialchars($post['post_picture'],ENT_QUOTES); ?>" width="15%" height="15%" alt="<?= htmlspecialchars($post['post_picture'],ENT_QUOTES); ?>"></a>
  </div>
<?php endif; ?>
<?php else: ?>
    <p>その投稿は削除されたか、URLが間違えています</p>
<?php endif; ?> 
  </div>
</div>
</body>
</html>
