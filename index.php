<?php
require_once('functions.php');

if (isset($_POST['submit'])) {

  $name = $_POST['name'];
  $name = htmlspecialchars($name, ENT_QUOTES);//セキュリティ対策

  $dbh = db_connect();//データベース接続

  $sql = 'INSERT INTO tasks (name, done) VALUES (?, 0)';//SQL文 name,doneカラムにデータを挿入する。?はプレースホルダ。0は初期値で、ここを変えるとタスク名を絞ることが可能。
  
  $stmt = $dbh->prepare($sql);//prepareはSQLが実行できる準備をして「PDOStatementクラス」のインスタンスを返す。
  $stmt->bindValue(1, $name, PDO::PARAM_STR);//ここで ?（プレースホルダ）にnameとユーザーから入力されたタスク名の値を紐づけている。
  $stmt->execute();//SQL文が実行され、データベースのテーブルにデータが格納される。

  $dbh = null;

  unset($name);
}

if (isset($_POST['method']) && ($_POST['method'] === 'put')) {//「済んだ」ボタンを押すと、methodというキーが存在し、NULL以外の値が設定されているかを確認し、かつ、putという文字列と等しいかチェック
  
  $id = $_POST["id"];
  $id = htmlspecialchars($id, ENT_QUOTES);
  $id = (int)$id;

  $dbh = db_connect();

  $sql = 'UPDATE tasks SET done = 1 WHERE id = ?';//該当するidのデータだけ、doneの値を1に変更する。
  $stmt = $dbh->prepare($sql);

  $stmt->bindValue(1, $id, PDO::PARAM_INT);
  $stmt->execute();

  $dbh = null;
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Todo リスト</title>
</head>
<body>
  <h1>Todo リスト</h1>
  <form action="index.php" method="post">
    <ul>
      <li>
        <span>タスク名</span>
        <input type="text" name="name">
      </li>
      <li>
        <input type="submit" name="submit">
      </li>
    </ul>
  </form>

  <ul>
    <?php
      $dbh = db_connect();

      $sql = 'SELECT id, name FROM tasks WHERE done = 0 ORDER BY id DESC';//doneの値が0のデータのみ取得する。
      $stmt = $dbh->prepare($sql);
      $stmt->execute();//SQL実行のメソッド　上の$sqlの構文が実行される
      $dbh = null;

      while ($task = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print '<li>';
        print $task["name"];//ボタンが押されると、タスク名に隠しデータで設定されているフォームの情報がPHPに送られる。
        print '
          <form action="index.php" method="post">
            <input type="hidden" name="method" value="put">
            <input type="hidden" name="id" value="' . $task['id'] . '">
            <button type="submit">済んだ</button>
          </form>
        ';
        print '</li>';
      }
    ?>
  </ul>

</body>
</html>

<!-- PDOクラスでデータベースを接続する時は、接続時にPDOインスタンスを作り、SQLの発行時にPDOStatementインスタンスを作ります。 -->
<!-- $stmt->fetchはPDOStatementインスタンスのfetchメソッド。データベースの結果セットから、次の行を取得する。PDO::FETCH_ASSOCは、連想配列の形で取得できる。 -->
<!-- whileループに入れているため、繰り返し行の取得が行われ、fetchメソッドが次の行の取得に失敗した場合、while(false)と評価されループが終了する。 -->