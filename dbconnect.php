<?php
  try {
    $dsn = 'mysql:dbname=tb220337db;host=localhost';
    $user = 'tb-220337';
    $password = 'AhrhUp4QPk';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
  }catch (PDOException $e){
    echo 'DB接続エラー：'.$e->getmessage();
  }