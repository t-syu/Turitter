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
    $unlink = $pdo -> prepare('SELECT member_posts.post_picture
    FROM member_posts WHERE id=?');
    $unlink -> execute(array($id));
    
    foreach ($unlink as $unl) {
      var_dump($unl['post_picture']); //foreachを使わないと、$unl['post_picture']は使えない。
      $filename = 'member_post_picture/'.$unl['post_picture']; //文字の連結は"."でやる by PHP
      unlink($filename); //これで、member_post_pictureの写真を消すことができる。
    }

    $del = $pdo -> prepare('DELETE FROM member_posts WHERE id=?');
    $del -> execute(array($id));
    
    // $filename ='member_post_pictire';
    // $fl=file($filename,FILE_IGNORE_NEW_LINES);
    // var_dump($fl);
    // $fl=file('/public_html/programming/member_post_picture',FILE_IGNORE_NEW_LINES);
    // var_dump($fl);
    // $fpw=fopen('/public_html/programming/member_post_picture',"w");
    // foreach($fl as $fline) {
    //   if( $fline !== $message['post_picture']) {
    //     fwrite($fpw,"$fline".PHP_EOL);
    //     echo 'Hit';
    //   }
    // }
  }
}

header('location: index.php');
exit();
?>