<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require('dbconnect.php');
require('get-exif-10from60.php');

if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) { //直接入られた時の防止
  $_SESSION['time'] = time();

  $members =$pdo -> prepare('SELECT * FROM members WHERE id=?');
  $members -> execute(array($_SESSION['id']));
  $member = $members -> fetch();
}else {
  header('location: login.php');
  exit();
}
if($_POST) {
  if($_POST['message']) { //<textarea cols="50" rows="5"></textarea>
    // var_dump($_POST['message']); output: NULL
    // $_POST['postimage']は絶対NULLとなる。POSTされず、$_FILESなどにしか反応しない。
    if($_FILES['postimage']['name']) { //NULLと''とemptyとissetはどれも別物!!
      $postImage = date("YmdHis").$_FILES['postimage']['name']; // Y/m/d H:i:sだとうまくいかない /が入ってるとurlに不具合が出るからだと思われる
      move_uploaded_file($_FILES['postimage']['tmp_name'],'member_post_picture/'.$postImage); //アップロードされたファイルがサーバー上で保存されているテンポラリファイルの名前 このfile関数で保存されている名前のこと
      $postFileName = $_FILES['postimage']['name'];
      if($postFileName) {
        $ext = substr($postFileName,-3);
        if($ext !== 'jpg' && $ext !== 'gif'  && $ext !== 'png' && $ext !== 'peg' && $ext !== 'JPG' && $ext !== 'GIF' && $ext !== 'PNG' && $ext !== 'PEG') {
          $error['image'] = 'type';
        }
      }
      $exif = @exif_read_data('member_post_picture/'.$postImage);

      if( !$exif ||
        !isset( $exif['GPSLatitudeRef'] ) || empty( $exif['GPSLatitudeRef'] ) ||
        !isset( $exif['GPSLatitude'] ) || empty( $exif['GPSLatitude'] ) ||
        !isset( $exif['GPSLongitudeRef'] ) || empty( $exif['GPSLongitudeRef'] ) ||
        !isset( $exif['GPSLongitude'] ) || empty( $exif['GPSLongitude'] )
	    ){
		// 案内メッセージ
	      echo '<script>alert(画像に位置情報、またはExif自体が含まれていませんでした…。)</script>' ;
	    }else {
        // 緯度を60進数から10進数に変換する
        $lat = get_10_from_60_exif( $exif['GPSLatitudeRef'] , $exif['GPSLatitude'] );

        // 経度を60進数から10進数に変換する
        $lng = get_10_from_60_exif( $exif['GPSLongitudeRef'] , $exif['GPSLongitude'] );
      }
      if(!$exif || !isset( $exif['DateTimeOriginal'])) {
        echo '<script>alert(画像に撮影日時、またはExif自体が含まれていませんでした…。)</script>' ;
      }else {
        $exifDatePattern = '/\A(?<year>\d{4}):(?<month>\d{1,2}):(?<day>\d{1,2}) (?<hour>\d{2}):(?<minute>\d{2}):(?<second>\d{2})\z/';

        if (preg_match($exifDatePattern, $exif['DateTimeOriginal'], $matches)) {
          $datetime = new \DateTime(sprintf('%d-%d-%d %d:%d:%d',
          $matches['year'],
          $matches['month'],
          $matches['day'],
          $matches['hour'],
          $matches['minute'],
          $matches['second']
        ));

          $datetime = $datetime -> format('Y-m-d H:i:s');
          $monthstamp = intval($matches['month']);
        }
      }
    }
    if($_POST['reply_post_id'] === '') { // なかったら、下でエラーが起きる。
      $_POST['reply_post_id'] = 0;
    }
    if($error['image'] !== 'type' && empty($_POST['search'])) { //二つ目はあってもなくても良い。検索が入ってる時、投稿が出来ないようにしている
      $message = $pdo -> prepare("INSERT INTO member_posts SET member_id=?, message=?, post_picture=?, lat=?, lng=?, monthstamp=?, datetime=?, reply_message_id=?, created=NOW()");
      $message -> execute(array(
        $member['id'],
        $_POST['message'],
        $postImage,
        $lat,
        $lng,
        $monthstamp,
        $datetime,
        $_POST['reply_post_id']
      ));
      
      header('location: index.php'); //更新した時同じものが繰り返して送信されないように、素の状態に戻す方法 そのため、ここをコメントアウトしない限りこの中のechoは見れなくなる
      exit();
    }
  }
  if($_POST['search'] || $_POST['hidden']) { //<input type='text' value="">
    // var_dump($_POST['search']); output: NULL
    if($_POST['search']) {
      $search = $_POST['search'];
    }
    if($_POST['hidden']) {
      $search = $_POST['hidden'];
    }
    $page = $_REQUEST['page'];
    if(empty($page)) {
      $page = 1;
    }
    $page = max($page, 1);
    $counts = $pdo -> query("SELECT COUNT(*) AS cnt FROM member_posts WHERE message LIKE '%".$search."%'");
    $cnt = $counts -> fetch();
    $maxPage = ceil($cnt['cnt']/5);
    $page = min($page, $maxPage);
    if(empty($page)) {
      $page = 1;
    }
    $start = ($page-1)*5;

    $member_posts = $pdo -> prepare("SELECT m.name, m.picture, p.* FROM members m, member_posts p WHERE m.id=p.member_id AND p.message LIKE '%".$search."%' ORDER BY p.created DESC LIMIT ?,5"); //OR m.name LIKE '%".$search."%'を入れるとユーザ検索も可能  OR p.created LIKE '%-".$search."-%' バグ発生
    $member_posts -> bindParam(1, $start, PDO::PARAM_INT); //1コ目の?に入れる値をintで入れる。
    $member_posts -> execute();
  }
}
if(empty($_POST['search']) && empty($_POST['hidden'])) {
  $page = $_REQUEST['page'];
  if(empty($page)) {
    $page = 1;
  }
  $page = max($page, 1);
  $counts = $pdo -> query('SELECT COUNT(*) AS cnt FROM member_posts');
  $cnt = $counts -> fetch();
  $maxPage = ceil($cnt['cnt']/5);
  $page = min($page, $maxPage);
  
  $start = ($page-1)*5;
  
  $member_posts = $pdo -> prepare('SELECT m.name, m.picture, p.* FROM members m, member_posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?,5');//queryで直接sqlを呼び出すことができる ?を使うと、prepareを使わなければならない membersをmとし、member_postsをpとショートカットさせた DESCは降順で並べるという意味
  $member_posts -> bindParam(1, $start, PDO::PARAM_INT); //execute で渡すと文字列として渡すことになってしまう。今回は、数字で入れなければならないのでINT 一つめの?だから1
  $member_posts -> execute();
}

if(isset($_REQUEST['res'])) { // 1.foreach $member_posts as $post の['id']と同じ数字 <a href="index.php?res=　の数字を受け取る 2.resというのは、URLから来ています。例えばhttp://example.com/index.php?res=1と、URLの最後に「?」に続けて「key=xxx」と指定するのを「URLパラメーター」というのですが、これを受け取れるのが「$_REQUEST」になります。URLに「res」が指定されていれば、自動的に「$_REQUEST['res']」という変数（配列の要素）ができあがります。 3.if文の{}がなくてもエラーでない、気をつけて
  //返信の処理
  $response = $pdo -> prepare('SELECT m.name, m.picture, p.* FROM members m, member_posts p WHERE m.id=p.member_id AND p.id=?');
  $response -> execute(array($_REQUEST['res']));

  $table = $response -> fetch();
  $message ='[＠'.$table['name'].' '.$table['message'].'] >RE ';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script type="text/javascript" src="/LKBNX-223J-rev-1-016/iine/cn/cn.php"></script> <!-- いいねボタン -->
	<title>Turitter.com</title>
	<link rel="stylesheet" href="style.css" />
</head>
<body>
<div id="wrap">
  <div id="post">
    <div id="head">
      <h6>Turitter.com</h6>
      <h1>釣りバカ日誌</h1>
    </div>
      <div style="text-align: left"><a href="google_maps.html">Google maps で魚の釣り情報を見つける</a></div>
      <div style="text-align: left"><a href="logout.php">ログアウト</a></div>
      <br>
      <form action="" method="post">
        検索:<input type="text" name="search" value="">
        <input type="submit" value="検索する">
      </form>
      <br>
      <div style="text-align: left"><a href="index.php">Topに戻る</a></div>
      <form action="" method="post"  enctype="multipart/form-data">
        <dl>
          <dt><?= htmlspecialchars($member['name'],ENT_QUOTES) ?>さん、メッセージをどうぞ</dt>
          <dd>
<?php if($message): ?>
            <textarea name="message" cols="50" rows="5"><?= htmlspecialchars($message,ENT_QUOTES); ?></textarea>
<?php else: ?>
            <textarea name="message" cols="50" rows="5"></textarea>
<?php endif; ?>
            <input type="hidden" name="reply_post_id" value="<?= htmlspecialchars($_REQUEST['res'],ENT_QUOTES); ?>" />
          </dd>
          <dd>
            <input type="file" name="postimage" />
            <?php if($error['image'] === 'type'): ?>
            <p class="error">*写真などは「.gif」または「.jpg」または「.png」の画像を指定して下さい </p>
            <?php endif; ?>
            <!-- <img src="member_post_picture/<?= htmlspecialchars($postImage,ENT_QUOTES);?>" width="48" height="48" alt="<?= htmlspecialchars($postImage,ENT_QUOTES);?>" /> -->
          </dd>
        </dl>
        <div>
          <p>
            <input type="submit" value="投稿する" />
          </p>
        </div>
      </form>
  </div>
  <div id="list">
<?php foreach ($member_posts as $post): ?>
  <div class="msg">
<?php if($post['picture'] !== NULL): ?>
    <img src="member_picture/<?= htmlspecialchars($post['picture'],ENT_QUOTES);?>" width="5%" height="5%" alt="<?= htmlspecialchars($post['name'],ENT_QUOTES);?>" />
<?php else:?>
    <img src="member_picture/nopictureuser.png" width='5%' height='5%' alt="nopictureuser.png">
<?php endif; ?>
    <p>
      <?= htmlspecialchars($post['message'],ENT_QUOTES);?><span class="name">（<?= htmlspecialchars($post['name'],ENT_QUOTES);?>）</span>
      [<a href="index.php?res=<?= htmlspecialchars($post['id'],ENT_QUOTES); ?>">Re</a>]</p>
      <p class="day"><a href="view.php?id=<?= htmlspecialchars($post['id'],ENT_QUOTES);?>"><?= htmlspecialchars($post['created'],ENT_QUOTES);?></a>
<?php if($post['reply_message_id'] > 0): ?>
      <a href="view.php?id=<?= htmlspecialchars($post['reply_message_id'],ENT_QUOTES);?>">返信元のメッセージ</a>
<?php endif; ?>
<?php if($_SESSION['id'] === $post['member_id']): ?>
      [<a href="delete.php?id=<?= htmlspecialchars($post['id'],ENT_QUOTES);?>" style="color: #F33;">削除</a>]
      [<a href="edit.php?id=<?= htmlspecialchars($post['id'],ENT_QUOTES);?>" style="color: #F33;">編集</a>]
<?php endif; ?>
    </p>
<?php if($post['post_picture'] !== NULL): ?> <!-- NULLと''は違う意味をもつ -->
  <p class="block">
    <a href="member_post_picture/<?= htmlspecialchars($post['post_picture'],ENT_QUOTES); ?>" target="_blank"><img src="member_post_picture/<?= htmlspecialchars($post['post_picture'],ENT_QUOTES); ?>" width="8%" height="8%" alt="<?= htmlspecialchars($post['post_picture'],ENT_QUOTES); ?>"></a>
  </p>
<?php endif; ?>
  </div>
<?php endforeach; ?>
  </div>
<?php if($find): ?>
  <!-- <p>該当結果がありませんでした</p> -->
<?php endif; ?>  
<div class="paging">
<?php if($page > 1): ?>
  <form action="index.php?page=<?= $page-1; ?>" method="post">
    <input type="submit" class="page" value="次のページへ" />
  <?php if($search): ?>
      <input type="hidden" name="hidden" value="<?= $search ?>">
  <?php endif; ?>
<?php endif; ?>
  </form> 
<?php if($page < $maxPage): ?>
  <form action="index.php?page=<?= $page+1; ?>" method="post">
    <input type="submit" class="page" value="前のページへ" />
  <?php if($search): ?>
    <input type="hidden" name="hidden" value="<?= $search ?>">
  <?php endif; ?>
<?php endif; ?>
  </form>
  </div>
</div>
</body>
</html>