<?php 
error_reporting(E_ALL & ~E_NOTICE);
// $_POST['name'] = '名前を入れて下さい';
// $_POST['email'] = 'メールアドレスを入れて下さい';
// $_POST['password'] = 'パスワードを入れて下さい';
session_start();
require('../dbconnect.php');
	
if($_POST) {
	if($_POST['name'] === '') {
		$error['name'] = 'blank';
	}
	if($_POST['email'] === '') {
		$error['email'] = 'blank';
	}
	if(strlen($_POST['password']) < 4) {
		$error['password'] = 'length';
	}
	if($_POST['password'] === '') {
		$error['password'] = 'blank';
	}
	$fileName = $_FILES['image']['name'];
	if($fileName) {
		$ext = substr($fileName,-3);
		if($ext !== 'jpg' && $ext !== 'gif'  && $ext !== 'png' && $ext !== 'peg' && $ext !== 'JPG' && $ext !== 'GIF' && $ext !== 'PNG' && $ext !== 'PEG') {
			$error['image'] = 'type';
		}
	}
	// print_r($error); Array ( [name] => blank [email] => blank [password] => blank ) 呼び出す時は、$error[0]ではなく、$error[name]にしなければならない
	
	//アカウント重複チェック
	if(empty($error)) {
		$member = $pdo -> prepare("SELECT COUNT(*) AS cnt FROM members WHERE email=?");
		$member -> execute(array($_POST['email']));
		$record = $member -> fetch();
		if($record['cnt'] > 0) {
			$error['email'] = 'duplicate'; //$error[email] = 'duplicate';であると、
			//Warning: Use of undefined constant email - assumed 'email' (this will throw an Error in a future version of PHP) in /public_html/section05/mini_bbs/join/index.php on line 37が出る
		}
	}

	if(!$error) { //$errorの配列全てに何もはいってなかったら、ここへ進む
		$_SESSION['join'] = $_POST; // こちらを先におかないと、	$_SESSION['join']['image'] = $image;が無駄になってしまう!!
		if($_FILES['image']['name']) {
			$image = date("YmdHis").$_FILES['image']['name']; // Y/m/d H:i:sだとうまくいかない /が入ってるとurlに不具合が出るからだと思われる
			move_uploaded_file($_FILES['image']['tmp_name'],'../member_picture/'.$image);
			$_SESSION['join']['image'] = $image;
		}

		header('location: check.php');
		exit();
	}
}
if($_REQUEST['action'] === 'rewrite' && isset($_SESSION['join'])) {
	$_POST = $_SESSION['join'];
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>

<div id="content">
<p>入会済みの方はこちらからどうぞ。</p>
<p>&raquo;<a href="../login.php">ログイン画面へ行く</a></p>
<p>次のフォームに必要事項をご記入ください。</p>
 <form action="" method="post" enctype="multipart/form-data">  <!--enctype="multipart/form-data"はファイルを読み込むときに必要-->
	<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>
		<dd>
					<input type="text" name="name" size="35" maxlength="255" value="<?= htmlspecialchars($_POST['name'],ENT_QUOTES)?>" />
					<?php if($error['name'] === 'blank') :?>
					<p class="error">*ニックネームを入れて下さい </p>
					<?php endif; ?>
		</dd>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="email" size="35" maxlength="255" value="<?= htmlspecialchars($_POST['email'],ENT_QUOTES)?>" />
					<?php if($error['email'] === 'blank'): ?>
					<p class="error">*メールアドレスを入れて下さい </p>
					<?php endif; ?>
					<?php if($error['email'] === 'duplicate'): ?>
					<p class="error">*指定されたメールアドレスはすでに登録されています </p>
					<?php endif; ?>
		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
        	<input type="password" name="password" size="10" maxlength="20" value="<?= htmlspecialchars($_POST['password'],ENT_QUOTES)?>" />
					<?php if($error['password'] === 'blank'): ?>
					<p class="error">*パスワードを入れて下さい </p>
					<?php endif; ?>
					<?php if($error['password'] === 'length'): ?>
					<p class="error">*4文字以上でパスワードを入れて下さい </p>
					<?php endif; ?>
        </dd>
		<dt>写真など</dt>
		<dd>
        	<input type="file" name="image" size="35" value="test"  />
					<?php if($error['image'] === 'type'): ?>
					<p class="error">*写真などは「.gif」または「.jpg」または「.png」の画像を指定して下さい </p>
					<?php endif; ?>
        </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>