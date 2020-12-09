<?php
  try {
    $dsn = 'mysql:dbname=**********;host=*********';
    $user = '*********';
    $password = '**********';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
  }catch (PDOException $e){
    echo 'DB接続エラー：'.$e->getmessage();
  }