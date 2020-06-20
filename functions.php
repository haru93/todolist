<?php
function db_connect() {

  try{

      $dsn = 'mysql:dbname=todolist;host=localhost;charset=utf8';
      $user = 'root';
      $password = '';
      
      $dbh = new PDO($dsn, $user, $password);//コンストラクタに渡してデータベースと接続
      $dbh->query('SET NAMES utf8');//文字化け対策
      $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

      return $dbh;
      
  } catch (PDOException $e) {
      print "エラー : " . $e->getMessage() . "<br/>";
      die();
  }

}